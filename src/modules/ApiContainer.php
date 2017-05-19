<?php

namespace tecnocen\roa\modules;

use Yii;
use tecnocen\roa\controllers\ApiContainerController;
use tecnocen\roa\urlRules\Container as ContainerUrlRule;
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
    public $containerUrlRuleClass = ContainerUrlRule::class;

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
        $app->urlManager->addRules([[
            'class' => $this->containerUrlRuleClass,
            'apiContainer' => $this,
        ]]);
    }

    public function getVersionModules()
    {
        $versions = [];
        foreach ($this->versions as $route => $config) {
            if (!$this->hasModule($route)) {
                $this->setModule($route, $config);
            }
            $versions[$route] = $this->getModule($route);
        }
        return $versions;
    }

    public function parseRules(ContainerUrlRule $urlRule)
    {
        // change the error handler and identityClass
        Yii::$app->errorHandler->errorAction = $this->errorAction;
        Yii::$app->user->identityClass = $this->identityClass;
        foreach ($this->versions as $route => $config) {
            $this->setModule($route, $config);
            $urlRule->addRule([
               'class' => $this->versionUrlRuleClass,
               'apiVersionId' => "{$this->uniqueId}/$route"
            ]);
        }
    }
}
