<?php

namespace app\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * Model class for table `{{%shop_sale}}`
 *
 * @property integer $id
 * @property Shop $shop
 * @property Employee $employee
 */
class Sale extends \yii\db\ActiveRecord
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
     * `getShop()`.
     */
    protected $shopClass = Shop::class;
    /**
     * @var string full class name of the model used in the relation
     * `getEmployee()`.
     */
    protected $employeeClass = Employee::class;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_sale}}';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'employee_id'], 'required'],
            [
                ['shop_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Shop::class,
                'targetAttribute' => ['shop_id' => 'id'],
            ],
            [
                ['employee_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Employee::class,
                'targetAttribute' => ['employee_id' => 'id'],
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
            'shop_id' => 'Shop ID',
            'employee_id' => 'Employee ID',
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
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(
            $this->employeeClass,
            ['id' => 'employee_id']
        );
    }
    /**
     * @inheritdoc
     */
    public static function find()
    {
        return parent::find()->where(['deleted' => null])->orWhere(['deleted' => false]);
    }
}
