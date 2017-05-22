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
class Container extends \yii\web\CompositeUrlRule
{
    /**
     * @var ApiVersion api version module that will be handled this rule
     */
    public $apiContainer;
    /**
     * @inheritdoc
     */
    public function createRules()
    {
        return []; // rules will be added on execution
    }

    private function ensureRules()
    {
       if (empty($this->rules)) {
           $this->apiContainer->parseRules($this);
       }
    }

    /**
     * @inheritdoc
     */
    public function parseRequest($manager, $request)
    {
        // only parse rules which start with the version id
        if (0 !== strpos($request->pathInfo, $this->apiContainer->uniqueId)) {
            return false;
        }
        $this->ensureRules();
        $result = parent::parseRequest($manager, $request);
        if ($result === false) {
            throw new NotFoundHttpException(
                "Unknown resource for '{$this->apiContainer->uniqueId}'"
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
