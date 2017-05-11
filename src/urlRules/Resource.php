<?php

namespace tecnocen\roa\urlRules;

use Yii;

/**
 * Default Url Rule to handle resources with collections.
 *
 * Supports representative URL using ownership slug.
*
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class Resource extends \yii\rest\UrlRule
{
    /**
     * @inheritdoc
     */
    public $pluralize = false;

    /**
     * @inheritdoc
     */
    public $tokens = ['{id}' => '<id:\d+>'];

    /**
     * @inheritdoc
     */
    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();
        foreach ($this->rules as $urlName => $rules) {
            foreach ($rules as $rule) {
                /* @var $rule \yii\web\UrlRule */
                $result = $rule->parseRequest($manager, $request);
                if (YII_DEBUG) {
                    Yii::trace([
                        'rule' => method_exists($rule, '__toString') ? $rule->__toString() : get_class($rule),
                        'match' => $result !== false,
                        'parent' => self::className()
                    ], __METHOD__);
                }
                if ($result !== false) {
                    return $result;
                }
            }
        }
        return false;
    }
}
