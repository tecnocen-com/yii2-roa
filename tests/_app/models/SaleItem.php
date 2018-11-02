<?php

namespace app\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * Model class for table `{{%shop_sale_item}}`
 *
 * @property integer $id
 * @property Sale $sale
 * @property Item $item
 */
class SaleItem extends \yii\db\ActiveRecord
{
    /**
     * @var string full class name of the model used in the relation
     * `getSale()`.
     */
    protected $saleClass = Sale::class;
    /**
     * @var string full class name of the model used in the relation
     * `getItem()`.
     */
    protected $itemClass = Item::class;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_sale_item}}';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sale_id', 'item_id'], 'required'],
            [
                ['sale_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Sale::class,
                'targetAttribute' => ['sale_id' => 'id'],
            ],
            [
                ['item_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Item::class,
                'targetAttribute' => ['item_id' => 'id'],
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sale_id' => 'Sale ID',
            'item_id' => 'Item ID',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSale()
    {
        return $this->hasOne(
            $this->saleClass,
            ['id' => 'sale_id']
        );
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(
            $this->itemClass,
            ['id' => 'item_id']
        );
    }
}
