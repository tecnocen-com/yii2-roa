<?php

namespace tecnocen\roa\urlRules;

interface UrlRuleCreator
{
    /**
     * Creates children url rules to be passed to a `Compoposite` url rule.
     *
     * @param Composite $urlRule the $urlRule object invoking the creator.
     * @return \yii\web\UrlRuleInterface[]
     */
    public function createUrlRules(Composite $urlRule);
}
