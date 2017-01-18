<?php

namespace tecnocen\roa\modules;

class ApiVersion extends \yii\base\Module
{
    const STABILITY_DEV = 'dev';
    const STABILITY_STABLE = 'stable';
    const STABILITY_DEPRECATED = 'deprecated';

    /**
     * @var string[]
     */
    public $resources = [];

    /**
     * @var string the stability level
     */
    public $stability = self::STABILITY_DEV;

    /**
     * @inheritdoc
     */
    public $controllerMap = ['index' => ApiVersionController::class];
}
