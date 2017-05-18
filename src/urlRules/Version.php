<?php

namespace tecnocen\roa\urlRules;

use Yii;
use yii\web\NotFoundHttpException;

/**
 * Internal url rule to handle the 
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class Version extends \yii\web\CompositeUrlRule
{
    /**
     * @var \tecnocen\roa\modules\ApiVersion api version module that will be
     * handled this rule
     */
    public $apiVersion;

    /**
     * @inheritdoc
     */
    public function createRules()
    {
        return []; // rules will be added on execution
    }

    /**
     * @inheritdoc
     */
    public function parseRequest($manager, $request)
    {
        // only parse rules which start with the version id
        if (0 !== strpos($request->pathInfo, $this->apiVersion->uniqueId)) {
            return false;
        }
        if (empty($this->rules)) { // attach version rules
            $this->apiVersion->parseRoutes($this);
        }
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
    public function addRule($ruleConfig, $prepend = false)
    {
        $rule = Yii::createObject($ruleConfig);
        $this->rules = $prepend
            ? array_merge([$rule], $this->rules)
            : array_merge($this->rules, [$rule]);
    }
 
    /**
     * @inheritdoc
     */
   public function createUrl($manager, $route, $params)
    {
        // only parse rules which start with the version id
        if (0 !== strpos($route, $this->apiVersion->uniqueId)) {
            return false;
        }
        if (empty($this->rules)) {
            $this->apiVersion->parseRoutes($this);
        }
        return parent::createUrl($manager, $route, $params);
    }
}
