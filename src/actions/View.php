<?php

namespace tecnocen\roa\actions;

use Yii;
use yii\data\ActiveDataProvider;

class View extends Action
{
    /**
     * @return ActiveDataProvider
     */
    public function run()
    {
        $request = Yii::$app->getRequest();
        $this->checkAccess(null, $request->getQueryParams());
        return $this->prepareDataProvider();
    }
    /**
     * Prepares the data provider that should return the requested collection of the models.
     * @return ActiveDataProvider
     */
    protected function prepareDataProvider()
    {
        if ($this->prepareDataProvider !== null) {
            return call_user_func($this->prepareDataProvider, $this);
        }
        /* @var $modelClass \yii\db\BaseActiveRecord */
        $modelClass = $this->modelClass;
        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $modelClass::find(),
        ]);
    }
}
