<?php

namespace tecnocen\roa;

use Yii;

class UrlRule extends \yii\rest\UrlRule
{
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
