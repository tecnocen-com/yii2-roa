<?php

namespace tecnocen\roa\actions;

use Yii;
use yii\base\Model;
use yii\web\ServerErrorHttpException;

/**
 * Action to update the attributes in a record.
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class Update extends Action
{
    use LoadFileTrait;

    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;

    /**
     * @var string[] that defines which columns will be recibe files
     */
    public $fileAttributes = [];

    /**
     * Updates an existing model.
     * @param mixed $id the primary key of the model.
     * @return \yii\db\ActiveRecordInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function run($id)
    {
        /* @var $model \yii\db\ActiveRecordInterface */
        $model = $this->findModel($id);
        $request = Yii::$app->getRequest();
        $this->checkAccess($model, $request->getQueryParams());
        $model->scenario = $this->scenario;
        $model->load(
            $request->getBodyParams() + $this->parseFileAttributes(),
            ''
        );
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }
}
