<?php

namespace tecnocen\roa\actions;

use yii\db\ActiveRecordInterface;

class Action extends \yii\rest\Action
{
    /**
     * @param ActiveRecordInterface $model
     * @param array $params
     * @throws \yii\web\HTTPException
     */
    public function checkAccess(ActiveRecordInterface $model, array $params = [])
    {
        $this->controller->checkAccess($this->id, $model, $params);
        if ($model->hasMethod('checkAccess')) {
            $model->checkAccess($params);
        }
        if (isset($this->checkAccess)) {
            call_user_func($this->checkAccess, $this, $model, $params);
        }
    }
}
