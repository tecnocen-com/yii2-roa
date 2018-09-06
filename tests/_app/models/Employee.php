<?php
namespace app\models;
/**
 * Model class for table `{{%shop_employee}}`
 *
 * @property integer $id
 * @property string $name
 *
 * @property Shop $shop
 */
class Employee extends \yii\db\ActiveRecord
{
    /**
     * @var string full class name of the model used in the relation
     * `getShop()`.
     */
    protected $shopClass = Shop::class;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_employee}}';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'name'], 'required'],
            [['name'], 'string', 'min' => 6],
            [['name'], 'unique'],
            [
                ['shop_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Shop::class,
                'targetAttribute' => ['shop_id' => 'id'],
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
            'name' => 'Employee Name',
            'shop_id' => 'Shop ID'
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShop()
    {
        return $this->hasOne(
            $this->shopClass,
            ['id' => 'shop_id']
        );
    }
}
