<?php

namespace tecnocen\roa\modules;

use tecnocen\roa\controllers\ApiVersionController;

class ApiVersion extends \yii\base\Module
{
    const STABILITY_DEV = 'dev';
    const STABILITY_STABLE = 'stable';
    const STABILITY_DEPRECATED = 'deprecated';

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'index';

    /**
     * @inheritdoc
     */
    public $controllerMap = ['index' => ApiVersionController::class];

    /**
     * @var string[]
     */
    public $resources = [];

    /**
     * @var string the stability level
     */
    public $stability = self::STABILITY_DEV;
}
