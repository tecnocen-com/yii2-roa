<?php

namespace tecnocen\roa\controllers;

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
     * @return string[]
     */
    public function actionIndex()
    {
        return $this->module->factSheet;
    }
}
