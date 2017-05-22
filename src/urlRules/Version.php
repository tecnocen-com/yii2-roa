<?php

namespace tecnocen\roa\urlRules;

use Yii;
use tecnocen\roa\modules\ApiVersion;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Internal url rule to handle the 
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class Version extends \yii\web\CompositeUrlRule
{
    /**
     * @var ApiVersion api version module that will be handled this rule
     */
    private $apiVersion;

    /**
     * @var string
     */
    public $apiVersionId;

    /**
     * @inheritdoc
     */
    public function createRules()
    {
        return []; // rules will be added on execution
    }

    private function ensureRules()
    {
       if (null !== $this->apiVersion) { // was already set
           return;
       }
       $this->apiVersion = Yii::$app->getModule($this->apiVersionId);
       if (null === $this->apiVersion
           || !$this->apiVersion instanceof ApiVersion
       ) {
           throw new InvalidConfigException(
               "'{$this->apiVersionId}' is not a valid api version"
           );
       }
       $this->apiVersion->parseRoutes($this);
    }

    /**
     * @inheritdoc
     */
    public function parseRequest($manager, $request)
    {
        // only parse rules which start with the version id
        if (0 !== strpos($request->pathInfo, $this->apiVersionId)) {
            return false;
        }
        $this->ensureRules();
        $result = parent::parseRequest($manager, $request);
        if ($result === false) {
            throw new NotFoundHttpException(
                "Unknown resource for '{$this->apiVersion->uniqueId}'"
            );
        }
        return $result;
    }

    /**
     * Adds a rule to the composition.
     *
     * @var array|\yii\web\UrlRuleInterface $ruleConfig
     * @return $this
     */
    public function addRule($ruleConfig)
    {
        $this->rules[] = Yii::createObject($ruleConfig);
    }
 
    /**
     * @inheritdoc
     */
   public function createUrl($manager, $route, $params)
    {
        // only parse rules which start with the version id
        if (0 !== strpos($route, $this->apiVersionId)) {
            return false;
        }
        $this->ensureRules();
        return parent::createUrl($manager, $route, $params);
    }
}
