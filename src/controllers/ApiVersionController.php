<?php

namespace tecnocen\roa\controllers;

/**
 * Shows the fact sheet for the api version its contained.
 *
 * @property \tecnocen\roa\modules\ApiVersion $module
 *
 * @author Angel (Faryshta) Guevara <aguevara@tecnocen.com>
 */
class ApiVersionController extends \yii\rest\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [];
    }

    /**
     * List of all the resources available for the parent module api version.
     *
     * @return array
     */
    public function actionIndex()
    {
        return $this->module->getFactSheet();
    }
}
