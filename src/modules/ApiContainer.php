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
        Yii::setAlias('@apiweb', $this->uniqueId);
        if (empty($this->errorAction)) {
            $this->errorAction = $this->uniqueId . '/index/error';
        }
        foreach ($this->versions as $route => $config) {
            $this->setModule($route, $config);
            $this->versions[$route] = $this->getModule($route);
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
