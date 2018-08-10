<?php

namespace tecnocen\roa\modules;

use tecnocen\oauth2server\Module as OAuth2Module;
use tecnocen\roa\controllers\ApiContainerController;
use tecnocen\roa\urlRules\Composite as CompositeUrlRule;
use tecnocen\roa\urlRules\Modular as ModularUrlRule;
use tecnocen\roa\urlRules\UrlRuleCreator;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\InvalidParamException;
use yii\base\Module;
use yii\web\UrlNormalizer;

/**
 * @author Angel (Faryshta) Guevara <aguevara@tecnocen.com>
 *
 * @var OAuth2Module $oauth2Module
 */
class ApiContainer extends Module implements UrlRuleCreator, BootstrapInterface
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
     * @var string the module id for the oauth2 server module.
     */
    public $oauth2ModuleId = 'oauth2';

    /**
     * @var array default OAuth2Module configuration.
     */
    private $oauth2Module = [
        'class' => OAuth2Module::class,
        'tokenParamName' => 'accessToken',
        'tokenAccessLifetime' => 3600 * 24,
        'storageMap' => [
        ],
        'grantTypes' => [
            'user_credentials' => [
                'class' => \OAuth2\GrantType\UserCredentials::class,
            ],
            'refresh_token' => [
                'class' => \OAuth2\GrantType\RefreshToken::class,
                'always_issue_new_refresh_token' => true
            ],
        ],
    ];

    /**
     * @var array module
     */
    public function setOauth2Module($module)
    {
        if (is_array($module)) {
            $this->setModule($this->oauth2ModuleId, array_merge(
                $this->oauth2Module,
                ['storageMap' => ['user_credentials' => $this->identityClass]],
                $module
            ));
        } elseif (!$module instanceof OAuth2Module) {
            $this->setModule($this->oauth2ModuleId, $module);
        } else {
            throw new InvalidParamException(
                static::class
                    . '::$oauth2Module must be an array or instance of '
                    . OAuth2Module::class
            );
        }
    }

    /**
     * @var \tecnocen\oauth2server\Module
     */
    public function getOauth2Module()
    {
        if (!$this->hasModule($this->oauth2ModuleId)) {
            $this->oauth2Module['storageMap']['user_credentials']
                = $this->identityClass;
            $this->setModule($this->oauth2ModuleId, $this->oauth2Module);
        }

        return $this->getModule($this->oauth2ModuleId);
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $this->getOauth2Module()->bootstrap($app);
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
