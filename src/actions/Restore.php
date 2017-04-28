<?php

namespace tecnocen\roa\actions;

use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Restores a record using the `restoreDelete()` method. Meant to be used with
 * library "yii2tech/ar-softdelete".
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class Restore extends Action
{
    /**
     * Applies the `restore()` method to a record.
     *
     * @param mixed $id the identifier value.
     */
    public function run($id)
    {
        $this->checkAccess(
            ($model = $this->findModel($id)),
            Yii::$app->request->queryParams
        );


        if (false === $model->restore()) {
            throw new ServerErrorHttpException(
                'Failed to restore the object for unknown reason.'
            );
        }
        return $model;
    }
}
