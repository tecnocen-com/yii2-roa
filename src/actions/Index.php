<?php

namespace tecnocen\roa\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

/**
 * Action to retreive a filtered and sorted collection based on a `$searchClass`
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class Index extends Action
{
    /**
     * @var string model class to retreive the records on the collection.
     */
    public $searchClass;

    /**
     * @var string name of the form containing the filter data.
     */
    public $formName = '';

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (empty($this->searchClass)) {
            throw new InvalidConfigException(
                get_class($this) . '::$searchClass must be set.'
            );
        }
    }

    /**
     * @return ActiveDataProvider|ActiveRecord
     */
    public function run()
    {
        $searchClass = $this->searchClass;
        /* @var $searchModel \yii\db\ActiveRecordInterface */
        $searchModel = new $searchClass();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            $this->formName
        );
        $this->checkAccess($searchModel, Yii::$app->request->getQueryParams());

        return $dataProvider ?: $searchModel;
    }
}
