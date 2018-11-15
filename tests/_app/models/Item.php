<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * Model class for table `{{%item}}`
 *
 * @property integer $id
 * @property string $name
 *
 * @property Sale[] $sales
 */
class Item extends \yii\db\ActiveRecord
{
    use SoftDeleteTrait;

    /**
     * @var string full class name of the model used in the relation
     * `getSale()`.
     */
    protected $saleItemClass = SaleItems::class;

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
     * @return ActiveQuery
     */
    public function getSaleItems(): ActiveQuery
    {
        return $this->hasMany($this->saleItemClass, ['item_id' => 'id'])
            ->inverseOf('item');
    }
}
