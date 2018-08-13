<?php

namespace tecnocen\roa\actions;

use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Deletes a record using the `softDelete()` method. Meant to be used with
 * library "yii2tech/ar-softdelete".
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class SoftDelete extends Action
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

        if (false === $model->softDelete()) {
            throw new ServerErrorHttpException(
                'Failed to delete the object for unknown reason.'
            );
        }
        Yii::$app->getResponse()->setStatusCode(204);
    }
}
