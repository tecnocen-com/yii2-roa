<?php

namespace tecnocen\roa\modules;

use tecnocen\roa\controllers\ApiContainerController;
use yii\helpers\ArrayHelper;
use yii\rest\UrlRule;
use Yii;

/**
 * @author Angel (Faryshta) Guevara <aguevara@tecnocen.com>
 */
class ApiContainer extends \yii\base\Module
    implements \yii\base\BootstrapInterface
{
    /**
     * @var string
     */
    public $identityClass;

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'index';

    public $baseNamespace;

    /**
     * @inheritdoc
     */
    public $controllerMap = ['index' => ApiContainerController::class];

    /**
     * @var array
     */
    public $versions = [];

    /**
     * @var string
     */
    public $errorAction;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        Yii::setAlias('@api', $this->uniqueId);
        if (empty($this->identityClass)) {
            $this->identityClass = "{$this->baseNamespace}\\models\\User";
        }
        if (empty($this->errorAction)) {
            $this->errorAction = $this->uniqueId . '/index/error';
        }
        foreach ($this->versions as $route => $config) {
            $this->setModule($route, ArrayHelper::merge([
                'class' => ApiVersion::class,
                'controllerNamespace' =>
                    "{$this->baseNamespace}\\$route\\controllers",
            ], $config));
            
            $resources = ArrayHelper::getValue($config, 'resources', []);
            if (!empty($resources)) {
                $controllers = [];
                $prefix = "{$this->uniqueId}/{$route}/";
                foreach ($resources as $key => $resource) {
                    $controllers[is_int($key) ? $resource : $key]
                        = "$prefix$resource";
                }
 
                $app->urlManager->addRules([[
                    'class' => UrlRule::class,
                    'controller' => $controllers,
                    'prefix' => $prefix,
                    'pluralize' => false,
                ]]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function createController($route)
    {
        // change the error handler and identityClass
        Yii::$app->errorHandler->errorAction = $this->errorAction;
        Yii::$app->user->identityClass = $this->identityClass;
        return parent::createController($route);
    }
}
