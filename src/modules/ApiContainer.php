<?php

namespace tecnocen\roa\modules;

use Yii;
use tecnocen\roa\controllers\ApiContainerController;
use tecnocen\roa\urlRules\Version as VersionUrlRule;
use yii\helpers\ArrayHelper;
use yii\rest\UrlRule;

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
     * @var string
     */
    public $versionUrlRuleClass = VersionUrlRule::class;

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
        if (empty($this->errorAction)) {
            $this->errorAction = $this->uniqueId . '/index/error';
        }
        $urlRuleClass = $this->versionUrlRuleClass;
        foreach ($this->versions as $route => $config) {
            $this->setModule($route, $config);
            $this->versions[$route] = $this->getModule($route);
            $app->urlManager->addRules([
                new $urlRuleClass(['apiVersion' => $this->versions[$route]])
            ]);
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
