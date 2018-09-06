<?php

namespace app\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * Model class for table `{{%safe_delete}}`
 *
 * @property integer $id
 *
 * @property SafeDelete[] $safeDelete
 */
class SafeDelete extends \yii\db\ActiveRecord
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
            ],
        ];
    }
    /**
     * @var string full class name of the model used in the relation
     * `getSageDeleteChild()`.
     */
    protected $safeDeleteChildClass = SafeDeleteChild::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%safe_delete}}';
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
     * @return \yii\db\ActiveQuery
     */
    public function getSafeDeleteChild()
    {
        return $this->hasMany($this->safeDeleteChildClass, ['safe_delete_id' => 'id'])
            ->inverseOf('safeDelete');
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return parent::find()->where(['deleted' => null]);
    }
}
