<?php

namespace tecnocen\roa\actions;

use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Deletes a record using the `safeDelete()` method. Meant to be used with
 * library "yii2tech/ar-softdelete".
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class SafeDelete extends Action
{
    /**
     * Applies the `softDelete()` method to a record.
     *
     * @param mixed $id the identifier value.
     */
    public function run($id)
    {
        $this->checkAccess(
            ($model = $this->findModel($id)),
            Yii::$app->request->queryParams
        );

        if (false === $model->safeDelete()) {
            throw new ServerErrorHttpException(
                'Failed to delete the object for unknown reason.'
            );
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}
