<?php

namespace tecnocen\roa\modules;

use DateTime;
use Yii;
use tecnocen\roa\controllers\ApiVersionController;
use yii\base\InvalidConfigException;

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

    public $controllerSubfix = 'Resource';

    private $controllerRoutes = [];

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
     * ```php
     * [
     *     'profile', // resources\ProfileResource
     *     'profile/history', // resources\profile\HistoryResource
     *     'image/<image_id:[\d]+>.<ext:[jpg|png]> => resources\ImageFileResource::class,
     *     'post' => resources\post\PostResource::class,
     *     'post/<post_id:[\d]+>/reply', // resources\post\ReplyResource
     * ]
     * ```
     */
    public $resources = [];

    public function getRoutes()
    {
        return array_keys($this->controllerRoutes);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($releaseTime = $this->calcTime($this->releaseDate)
            && $releaseTime <= ($now = time())
        ) {
            if ($deprecationTime = $this->calcTime($this->deprecationDate)
                && $obsoleteTime = $this->calcTime($this->obsoleteDate)
            ) {
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

                if ($obsoleteTime > $now) {
                    $this->stability = self::STABILITY_OBSOLETE;
                } elseif ($deprecationTime > $now) {
                    $this->stability = self::STABILITY_DEPRECATED;
                } else {
                    $this->stability = self::STABILITY_STABLE;
                }
            } else {
                $this->stability = self::STABILITY_STABLE;
            }
        }

        if ($this->stability !== self::STABILITY_OBSOLETE) {
            $this->buildRoutes();
        }
    }

    private function buildRoutes()
    {
        $this->controllerNamespace = rtrim($this->controllerNamespace, '\\') . '\\';
        foreach ($this->resources as $route => $class) {
            $route = is_int($route) ? $class : $route;
            $controllerRoute = $this->buildControllerRoute($route);
            if (strpos($class, '\\') === false) {
                $class = $this->buildControllerClass($controllerRoute);
            }

            $this->controllerMap[$controllerRoute] = $class;
            $this->controllerRoutes[$route] = "{$this->uniqueId}/$controllerRoute";

        }
        Yii::$app->urlManager->addRules([[
            'class' => \yii\rest\UrlRule::class,
            'controller' => $this->controllerRoutes,
            'prefix' => $this->uniqueId,
            'pluralize' => false,
        ]]);
    }

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


    private function buildControllerClass($controllerRoute)
    {
        $lastSeparator = strrpos($controllerRoute, '--');
        if ($lastSeparator === false) {
            $lastClass = $controllerRoute;
            $ns = '';
        } else {
            $lastClass = substr($controllerRoute, $lastSeparator + 2);
            $ns = substr($controllerRoute, 0, $lastSeparator);
        }
        return $this->controllerNamespace
            . strtr($ns, ['--' => '\\'])
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
}
