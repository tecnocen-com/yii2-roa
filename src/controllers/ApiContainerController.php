<?php

namespace tecnocen\roa\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NoutFoundHttpException;

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
        return ArrayHelper::map(
            $this->module->versionModules,
            'id',
            'factSheet'
        );
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

    /**
     * Action shown when a resource is  no longer available.
     *
     * @throws GoneHttpException
     */
    public function actionGone()
    {
        throw new GoneHttpException(
            'The resource you seek is obsolete, visit '
                . Url::to(['index'])
                . ' to get the fact sheets of all available versions.'
        );
    }
}	
