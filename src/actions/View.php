<?php

namespace tecnocen\roa\actions;

use Yii;

class View extends Action
{
    /**
     * @return ActiveDataProvider
     */
    public function run($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);
        $request = Yii::$app->getRequest();
        $this->checkAccess($model, $request->getQueryParams());
        return $model;
    }
}
