<?php

namespace tecnocen\roa\modules;

use tecnocen\roa\controllers\ApiContainerController;
use yii\helpers\ArrayHelper;
use yii\rest\UrlRule;

class ApiContainer extends \yii\base\Module
    implements \yii\base\BootstrapInterface
{
    public $identand identityClassass;
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
        $this->setAlias('@api', $this->uniqueId);
        $class = $this::class;
        $namespace = substr($class, 0, strrpos($class, '\\'));
        if (empty($this->identityClass)) {
            $this->identityClass = "$namespace\\models\\User";
        }
        if (empty($this->errorAction)) {
            $this->errorAction = $this->uniqueId . '/index/error';
        }
        foreach ($this->versions as $route => $config) {
            $this->addModule($route, ArrayHelper::merge([
                'class' => ApiVersion::class,
                'controllerNamespace' => "$namespace\\$route",
            ], $config));
            $version = $this->getModule($id);
            $resources = $version->resources;
            $prefix = "{$this->uniqueId}/{$route}/";
            array_walk($resources, function (&$resource) use ($prefix) {
                $resource = "$prefix/$resource";
            });
            $app->urlManager->addRules([[
                'class' => UrlRule::class,
                'controllers' => $resources,
                'prefix' => $prefix,
            ]]);
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
