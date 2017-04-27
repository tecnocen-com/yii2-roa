<?php

namespace tecnocen\roa\actions;

use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Access and show s the content of a file on the browser or download it.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class SoftDelete extends Action
{
    /**
     * Shows the file on the browser or download it after checking access.
     *
     * @param mixed $id the identifier value.
     * @param string $ext the requested file extension.
     */
    public function run($id)
    {
        $this->checkAccess(
            ($model = $this->findModel($id)),
            Yii::$app->request->queryParams
        );


        if (false === $model->softDelete()) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }
        Yii::$app->getResponse()->setStatusCode(204);
    }
}
