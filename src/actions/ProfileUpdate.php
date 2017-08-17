<?php

namespace tecnocen\roa\actions;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

class ProfileUpdate extends \yii\rest\Action
{
    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;

    /**
     * @inheritdoc
     */
    public function init()
    {
    }

    /**
     * Updates the information of the logged user.
     *
     * @return \yii\db\ActiveRecordInterface
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function run()
    {
        /* @var $model ActiveRecord */
        $model = Yii::$app->user->identity;
        $model->scenario = $this->scenario;
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }
}
