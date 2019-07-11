<?php

namespace tecnocen\roa\urlRules;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Url Rule to handle modules implementing the `UrlRuleCreator` interface.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class Modular extends Composite
{
    /**
     * @inheritdoc
     *
     * can accept parameters.
     *
     * - {moduleId}: the unique module id associated to this rule.
     */
    public $notFoundMessage = 'Unknown route for module `{moduleId}`.';

    /**
     * @var string unique id to grab the module from the application that will
     * parse the rules.
     */
    public $moduleId;

    /**
     * @inheritdoc
     */
    protected function isApplicable(string $route): bool
    {
        // only parse rules which start with the module id
        return 0 === strpos($route, $this->moduleId);
    }

    /**
     * @inheritdoc
     */
    protected function createRules()
    {
        if (empty($this->moduleId)) {
            throw new InvalidConfigException('`moduleId` must be set.');
        }
        $module = Yii::$app->getModule($this->moduleId);
        if (!$module instanceof UrlRuleCreator) {
            throw new InvalidConfigException(
                "Module `{$this->moduleId}` must implement "
                    . UrlRuleCreator::class
            );
        }

        return $module->createUrlRules($this);
    }

    /**
     * @inheritdoc
     */
    protected function createNotFoundException()
    {
        return new NotFoundHttpException(
            strtr($this->notFoundMessage, ['{moduleId}' => $this->moduleId])
        );
    }
}
