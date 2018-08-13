<?php

namespace tecnocen\roa\actions;

use Yii;

class View extends Action
{
    /**
     * @return ActiveDataProvider
     * @param mixed $id
     */
    public function run($id)
    {
        /* @var $model \yii\db\ActiveRecordInterface */
        $model = $this->findModel($id);
        $request = Yii::$app->getRequest();
        $this->checkAccess($model, $request->getQueryParams());

        return $model;
    }
}
