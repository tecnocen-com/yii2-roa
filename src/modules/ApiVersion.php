<?php

namespace tecnocen\roa\modules;

use DateTime;
use Yii;
use tecnocen\roa\controllers\ApiVersionController;
use tecnocen\roa\urlRules\Version as VersionUrlRule;
use tecnocen\roa\urlRules\Resource as ResourceUrlRule;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\UrlManager;

/**
 * Class to attach a version to an `ApiContainer` module.
 *
 * You can control the stability by setting the properties `$releaseDate`,
 * `$deprecationDate` and `$obsoleteDate`.
 *
 * The resources are declared using the `$resources` array property
 */
class ApiVersion extends \yii\base\Module
{
    const STABILITY_DEVELOPMENT = 'development';
    const STABILITY_STABLE = 'stable';
    const STABILITY_DEPRECATED = 'deprecated';
    const STABILITY_OBSOLETE = 'obsolete';

    /**
     * @var string subfix used to create the default classes
     */
    public $controllerSubfix = 'Resource';

    /**
     * @var string full class name which will be used as default for routing.
     */
    public $urlRuleClass = ResourceUrlRule::class;

    /**
     * @var string date in Y-m-d format for the date at which this version
     * became stable
     */
    public $releaseDate;

    /**
     * @var string date in Y-m-d format for the date at which this version
     * became deprecated
     */
    public $deprecationDate;

    /**
     * @var string date in Y-m-d format for the date at which this version
     * became obsolete
     */
    public $obsoleteDate;

    /**
     * @var string the stability level
     */
    protected $stability = self::STABILITY_DEVELOPMENT;

    /**
     * @return string the stability defined for this version.
     */
    public function getStability()
    {
        return $this->stability;
    }

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'index';

    /**
     * @inheritdoc
     */
    public $controllerMap = ['index' => ApiVersionController::class];

    /**
     * @var string[] list of 'patternRoute' => 'resource' pairs to connect a
     * route to a resource. if no key is used, then the value will be the
     * pattern too.
     *
     * Special properties:
     *
     * - urlRule array the configuration for how the routing url rules will be
     *   created before attaching them to urlManager.
     *
     * ```php
     * [
     *     'profile', // resources\ProfileResource
     *     'profile/history', // resources\profile\HistoryResource
     *     'profile/image' => [
     *         'class' => resources\profile\ImageResource::class,
     *         'urlRule' => ['class' => 'tecnocen\\roa\\urlRules\\File'],
     *     ],
     *     'post' => ['class' => resources\post\PostResource::class],
     *     'post/<post_id:[\d]+>/reply', // resources\post\ReplyResource
     * ]
     * ```
     */
    public $resources = [];

    /**
     * @return string[] gets the list of routes allowed for this api version.
     */
    public function getRoutes()
    {
        $routes = [$this->uniqueId];
        foreach ($this->resources as $index => $value) {
            $routes[] = $this->uniqueId . '/'
                . (is_string($index) ? $index : $value);
        }
        return $routes;
    }

    /**
     * @return array stability, life cycle and resources for this version. 
     */
    public function getFactSheet()
    {
        return [
            'stability' => $this->stability,
            'lifeCycle' => [
                'releaseDate' => $this->releaseDate,
                'deprecationDate' => $this->deprecationDate,
                'obsoleteDate' => $this->obsoleteDate,
            ],
            'routes' => $this->getRoutes(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $releaseTime = $this->calcTime($this->releaseDate);
        $now = time();
        if ($releaseTime !== null && $releaseTime <= $now) {
            $deprecationTime = $this->calcTime($this->deprecationDate);
            $obsoleteTime = $this->calcTime($this->obsoleteDate);
            if ($deprecationTime !== null && $obsoleteTime !== null) {
                if ($obsoleteTime < $deprecationTime) {
                    throw new InvalidConfigException(
                        'The `obsoleteDate` must not be earlier than `deprecationDate`'
                    );
                }
                if ($deprecationTime < $releaseTime) {
                    throw new InvalidConfigException(
                        'The `deprecationDate` must not be earlier than `releaseDate`'
                    );
                }

                if ($obsoleteTime < $now) {
                    $this->stability = self::STABILITY_OBSOLETE;
                } elseif ($deprecationTime < $now) {
                    $this->stability = self::STABILITY_DEPRECATED;
                } else {
                    $this->stability = self::STABILITY_STABLE;
                }
            } else {
                $this->stability = self::STABILITY_STABLE;
            }
        }
    }

    /**
     * @return array list of configured urlrules by default
     */
    protected function extraRoutes()
    {
        return [
            [
                'class' => \yii\web\UrlRule::class,
                'pattern' => $this->uniqueId,
                'route' => $this->uniqueId,
            ]
        ];
    }

    /**
     * Parse the routes and attaches the routing rules to a composite rule.
     *
     * @param VersionUrlRule $urlRule
     */
    public function parseRoutes(VersionUrlRule $urlRule)
    {
        foreach ($this->extraRoutes() as $ruleConfig) {
            $urlRule->addRUle($ruleConfig);
        }
        if ($this->stability == self::STABILITY_OBSOLETE) {
            $urlRule->addRule([
                'class' => \yii\web\UrlRule::class,
                'pattern' => $this->uniqueId . '/<route:*+>',
                'route' => $this->uniqueId . '/index/gone',
            ]);
            return;
        }

        foreach ($this->resources as $route => $controller) {
            $route = is_int($route) ? $controller : $route;
            $controllerRoute = $this->buildControllerRoute($route);
            if (is_string($controller)) {
                $controller = [
                    'class' => $this->buildControllerClass($controllerRoute),
                ];
            } elseif (is_array($controller) && empty($controller['class'])) {
                $controller['class'] = $this->buildControllerClass(
                    $controllerRoute
                );
            }
            $urlRule->addRule(array_merge(
                [
                    'class' => $this->urlRuleClass,
                    'controller' => [
                        $route =>  "{$this->uniqueId}/$controllerRoute"
                    ],
                    'prefix' => $this->uniqueId,
                ],
                ArrayHelper::remove($controller, 'urlRule', [])
            ));
            $this->controllerMap[$controllerRoute] = $controller;
        }
    }

    /**
     * Converts a ROA route to an MVC route to be handled by `$controllerMap`
     *
     * @param string $roaRoute
     * @return string
     */
    private function buildControllerRoute($roaRoute)
    {
        return strtr(
            preg_replace(
                '/\/\<.*?\>\//',
                '--',
                $roaRoute
            ),
            ['/' => '-']
        );
    }

    /**
     * Converts an MVC route to the default controller class.
     *
     * @param string $controllerRoute
     * @return string
     */
    private function buildControllerClass($controllerRoute)
    {
        $lastSeparator = strrpos($controllerRoute, '--');
        if ($lastSeparator === false) {
            $lastClass = $controllerRoute;
            $ns = '';
        } else {
            $lastClass = substr($controllerRoute, $lastSeparator + 2);
            $ns = substr($controllerRoute, 0, $lastSeparator + 2);
        }
        return $this->controllerNamespace
            . '\\' . strtr($ns, ['--' => '\\'])
            . str_replace(' ', '', ucwords(str_replace('-', ' ', $lastClass)))
            . $this->controllerSubfix;
    }

    /**
     * @param string date in 'Y-m-d' format
     * @return integer unix timestamp
     */
    private function calcTime($date)
    {
         if ($date === null) {
             return null;
         }
         if (false === ($dt = DateTime::createFromFormat('Y-m-d', $date))) {
             throw new InvalidConfigException(
                 'Dates must use the "Y-m-d" format.'
             );
         }

         return $dt->getTimestamp();
    }
}
