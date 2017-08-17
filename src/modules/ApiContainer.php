<?php

namespace tecnocen\roa\modules;

use tecnocen\roa\controllers\ApiContainerController;
use tecnocen\roa\urlRules\Composite as CompositeUrlRule;
use tecnocen\roa\urlRules\Modular as ModularUrlRule;
use tecnocen\roa\urlRules\UrlRuleCreator;
use Yii;
use yii\web\UrlNormalizer;

/**
 * @author Angel (Faryshta) Guevara <aguevara@tecnocen.com>
 */
class ApiContainer extends \yii\base\Module
    implements UrlRuleCreator, \yii\base\BootstrapInterface
{
    /**
     * @var string
     */
    public $identityClass;

    /**
     * @var string
     */
    public $versionUrlRuleClass = ModularUrlRule::class;

    /**
     * @var string
     */
    public $containerUrlRuleClass = ModularUrlRule::class;

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
            'moduleId' => $this->uniqueId,
            'normalizer' => [
                'action' => UrlNormalizer::ACTION_REDIRECT_PERMANENT,
            ],
        ]]);
    }

    /**
     * @return ApiVersion[] return all the versions attached to the container
     * indexed by their respective id.
     */
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

    /**
     * @return \yii\web\UrlRuleInterface[]
     */
    protected function defaultUrlRules()
    {
        return [
            Yii::createObject([
                'class' => \yii\web\UrlRule::class,
                'pattern' => $this->getUniqueId(),
                'route' => $this->getUniqueId(),
            ]),
        ];
    }

    /**
     * @inheritdoc
     */
    public function createUrlRules(CompositeUrlRule $urlRule)
    {
        // change the error handler and identityClass
        Yii::$app->errorHandler->errorAction = $this->errorAction;
        Yii::$app->user->identityClass = $this->identityClass;
        $rules = $this->defaultUrlRules();
        foreach ($this->versions as $route => $config) {
            $this->setModule($route, $config);
            $rules[] = Yii::createObject([
               'class' => $this->versionUrlRuleClass,
               'moduleId' => "{$this->uniqueId}/$route",
            ]);
        }

        return $rules;
    }

    /**
     * @return string HTTP Url linking to this module
     */
    public function getSelfLink()
    {
        return Url::to(['//' . $this->getUniqueId()]);
    }
}
