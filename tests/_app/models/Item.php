<?php

namespace app\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * Model class for table `{{%item}}`
 *
 * @property integer $id
 * @property string $name
 *
 * @property Sale[] $sale
 */
class Item extends \yii\db\ActiveRecord
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
     * @var string full class name of the model used in the relation
     * `getSale()`.
     */
    protected $saleClass = Sale::class;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%item}}';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'min' => 6],
            [['name'], 'unique'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Item Name',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSales()
    {
        return $this->hasMany($this->saleClass, ['item_id' => 'id'])
            ->inverseOf('item');
    }
    /**
     * @inheritdoc
     */
    public static function find()
    {
        return parent::find()->where(['deleted' => null])->orWhere(['deleted' => false]);
    }
}
