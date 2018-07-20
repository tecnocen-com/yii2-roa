<?php

namespace tecnocen\roa\hal;

use yii\base\Component;
use yii\helpers\Json;
use yii\web\ResponseFormatterInterface;

/**
 * @inheritdoc
 */
class JsonResponseFormatter extends \yii\web\JsonResponseFormatter
{
    /**
     * @inheritdoc
     */
    public $contentType = self::CONTENT_TYPE_HAL_JSON;
}
