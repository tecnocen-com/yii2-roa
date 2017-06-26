<?php

namespace tecnocen\roa\actions;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\web\ServerErrorHttpException;

class Update extends Action
{
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
     * @param string $id the primary key of the model.
     * @return \yii\db\ActiveRecordInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function run($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);
        $request = Yii::$app->getRequest();
        $this->checkAccess($model, $request->getQueryParams());
        $model->scenario = $this->scenario;
        $model->load($request->getBodyParams(), '');
        foreach ($this->fileAttributes as $attribute => $value) {
            if (is_int($attribute)) {
                $attribute = $value;
            }
            if (null !== ($uploadedFile = UploadedFile::getInstanceByName(
                $value
            ))) {
                $model->$attribute = $uploadedFile;
            }
        }
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }
        return $model;
    }
}
