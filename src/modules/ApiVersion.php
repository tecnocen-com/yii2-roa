<?php

namespace tecnocen\roa\modules;

use DateTime;
use tecnocen\roa\controllers\ApiVersionController;
use tecnocen\roa\urlRules\Composite as CompositeUrlRule;
use tecnocen\roa\urlRules\Resource as ResourceUrlRule;
use tecnocen\roa\urlRules\UrlRuleCreator;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsonResponseFormatter;
use yii\web\Response;
use yii\web\UrlManager;
use yii\web\XmlResponseFormatter;

/**
 * Class to attach a version to an `ApiContainer` module.
 *
 * You can control the stability by setting the properties `$releaseDate`,
 * `$deprecationDate` and `$obsoleteDate`.
 *
 * The resources are declared using the `$resources` array property
 */
class ApiVersion extends \yii\base\Module implements UrlRuleCreator
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
     * @var string URL where the api documentation can be found.
     */
    public $apidoc = null;

    /**
     * @var array|ResponseFormatterInterface[] response formatters which will
     * be attached to `Yii::$app->response->formatters`. By default just enable
     * HAL responses.
     */
    public $responseFormatters = [
        Response::FORMAT_JSON => [
            'class' => JsonResponseFormatter::class,
            'contentType' => JsonResponseFormatter::CONTENT_TYPE_HAL_JSON,
        ],
        Response::FORMAT_XML => [
            'class' => XmlResponseFormatter::class,
            'contentType' => 'application/hal+xml',
        ],
    ];

    /**
     * @var string the stability level
     */
    protected $stability = self::STABILITY_DEVELOPMENT;

    /**
     * @return string the stability defined for this version.
     */
    public function getStability(): string
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
    public function getRoutes(): array
    {
        $routes = ['/'];
        foreach ($this->resources as $index => $value) {
            $routes[] =
                (is_string($index) ? $index : $value);
        }

        return $routes;
    }

    /**
     * @return array stability, life cycle and resources for this version.
     */
    public function getFactSheet(): array
    {
        return [
            'stability' => $this->stability,
            'lifeCycle' => [
                'releaseDate' => $this->releaseDate,
                'deprecationDate' => $this->deprecationDate,
                'obsoleteDate' => $this->obsoleteDate,
            ],
            'routes' => $this->getRoutes(),
            '_links' => [
                'self' => $this->getSelfLink(),
                'apidoc' => $this->apidoc,
            ],
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
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        foreach ($this->responseFormatters as $id => $responseFormatter) {
            Yii::$app->response->formatters[$id] = $responseFormatter;
        }

        return true;
    }

    /**
     * @return array list of configured urlrules by default
     */
    protected function defaultUrlRules()
    {
        return [
            Yii::createObject([
                'class' => \yii\web\UrlRule::class,
                'pattern' => $this->uniqueId,
                'route' => $this->uniqueId,
                'normalizer' => ['class' => \yii\web\UrlNormalizer::class],
            ]),
        ];
    }

    /**
     * @inheritdoc
     */
    public function createUrlRules(CompositeUrlRule $urlRule): array
    {
        $rules = $this->defaultUrlRules();
        if ($this->stability == self::STABILITY_OBSOLETE) {
            $rules[] = Yii::createObject([
                'class' => \yii\web\UrlRule::class,
                'pattern' => $this->uniqueId . '/<route:*+>',
                'route' => $this->uniqueId . '/index/gone',
            ]);

            return $rules;
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
            $rules[] = Yii::createObject(array_merge(
                [
                    'class' => $this->urlRuleClass,
                    'controller' => [
                        $route => "{$this->uniqueId}/$controllerRoute",
                    ],
                    'prefix' => $this->uniqueId,
                ],
                ArrayHelper::remove($controller, 'urlRule', [])
            ));
            $this->controllerMap[$controllerRoute] = $controller;
        }

        return $rules;
    }

    /**
     * Converts a ROA route to an MVC route to be handled by `$controllerMap`
     *
     * @param string $roaRoute
     * @return string
     */
    private function buildControllerRoute(string $roaRoute): string
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
    private function buildControllerClass(string $controllerRoute): string
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
     * @param string $date in 'Y-m-d' format
     * @return ?int unix timestamp
     */
    private function calcTime($date): ?int
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

    /**
     * @return string HTTP Url linking to this module
     */
    public function getSelfLink(): string
    {
        return Url::to(['//' . $this->getUniqueId()], true);
    }
}
