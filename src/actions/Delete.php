<?php

namespace tecnocen\roa\actions;

use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Deletes a record from the database.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class Delete extends Action
{
    /**
     * Applies the `softDelete()` method to a record.
     *
     * @param mixed $id the identifier value.
     */
    public function run($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);
        $this->checkAccess($model, Yii::$app->request->getQueryParams());

        if (false === $model->delete()) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }
        Yii::$app->getResponse()->setStatusCode(204);
    }
}
