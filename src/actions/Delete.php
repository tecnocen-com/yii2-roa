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
     * Applies the `delete()` method to a record.
     *
     * @param mixed $id the identifier value.
     */
    public function run($id)
    {
        $this->checkAccess(
            ($model = $this->findModel($id)),
            Yii::$app->request->getQueryParams()
        );

        if (false === $model->delete()) {
            throw new ServerErrorHttpException(
                'Failed to delete the object for unknown reason.'
            );
        }
        Yii::$app->getResponse()->setStatusCode(204);
    }
}
