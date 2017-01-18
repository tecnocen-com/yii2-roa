<?php

namespace tecnocen\roa\controllers;

use yii\helpers\ArrayHelper;
use yii\web\NoutFoundHttpException;

class ApiContainerController extends \yii\rest\Controller
{
    /**
     * Lists the available versions and their respective stability for the
     * parent module.
     *
     * @return string[]
     */
    public function indexAction()
    {
        return ArrayHelper::map($this->module->modules, 'id', 'stability');
    }

    /**
     * Handles the exceptions catched by the system bootstrapping process.
     * @return \Exception
     */
    public function actionError()
    {
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            $exception = new NotFoundHttpException(
                Yii::t('yii', 'Page not found.')
            );
        }

        Yii::$app->response->statusCode = isset($exception->statusCode)
            ? $exception->statusCode
            : 500;

        return $exception;
    }
}	
