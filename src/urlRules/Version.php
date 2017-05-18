<?php

namespace tecnocen\roa\urlRules;

use Yii;

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
        if (0 !== strpos($this->apiVersion->uniqueId, $request->pathInfo)) {
            return false;
        }
        if (empty($this->rules)) { // attach version rules
            $this->apiVersion->parseRoutes($this);
        }
        $result = parent::parseRequest($manager, $request);
        if ($result === false) {
            throw new NotFoundHttpException(
                "Unknown resource for '{$this->versionId}'"
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
        if (strpos($this->versionId, $route) !== 0) {
            return false;
        }
        if (empty($this->rules)) {
            $this->apiVersion->parseRoutes($this);
        }
        return parent::createUrl($manager, $route, $params);
    }
}
