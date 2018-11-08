<?php

namespace app\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * Model class for table `{{%shop}}`
 *
 * @property integer $id
 * @property string $name
 */
class ShopRecovery extends \yii\db\ActiveRecord
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
                ]
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop}}';
    }
    /**
     * @inheritdoc
     */
    public static function find()
    {
        return parent::find()->where(['deleted' => true]);
    }
}
