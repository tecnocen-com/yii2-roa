<?php

namespace app\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * Model class for table `{{%soft_delete}}`
 *
 * @property integer $id
 *
 */
class SoftDelete extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'deleted' => true
                ],
                'replaceRegularDelete' => false
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%soft_delete}}';
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }
    /**
     * @inheritdoc
     */
    public static function find()
    {
        return parent::find()->where(['deleted' => null])->orWhere(['deleted' => false]);
    }
}
