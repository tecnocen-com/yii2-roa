<?php

namespace tecnocen\roa\actions;

class Action extends \yii\rest\Action
{
    /**
     * @param ActiveRecord $model
     * @param array $params
     * @throws \yii\web\HTTPException
     */
    public function checkAccess($model, array $params = [])
    {
        $this->controller->checkAccess($this->id, $model, $params);
        if ($model->hasMethod('checkAccess')) {
            $model->checkAccess($params);
        }
        if (isset($this->checkAccess)) {
            call_user_func($this->checkAccess, $model, $params);
        }
    }
}
