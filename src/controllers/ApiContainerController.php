<?php

namespace tecnocen\roa\controllers;

use yii\helpers\ArrayHelper;
use yii\web\NoutFoundHttpException;
use Yii;

/**
 * Lists all the available versions for an api and handles error responses.
 *
 * @author Angel (Faryshta) Guevara <aguevara@tecnocen.com>
 */
class ApiContainerController extends \yii\rest\Controller
{
    /**
     * Lists the available versions and their respective stability for the
     * parent module.
     *
     * @return string[]
     */
    public function actionIndex()
    {
        $versions = [];
        foreach ($this->module->versions as $id => $config) {
            $versions[$id] = ArrayHelper::getValue($config, 'stability', 'dev');
        }
        return $versions;
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
