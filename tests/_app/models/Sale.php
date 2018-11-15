<?php

namespace app\models;

/**
 * Model class for table `{{%shop_sale}}`
 *
 * @property integer $id
 * @property integer $shop_id
 * @property integer $employee_id
 *
 * @property Shop $shop
 * @property Employee $employee
 */
class Sale extends \yii\db\ActiveRecord
{
    use SoftDeleteTrait;

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
     * @return SoftDeleteQuery
     */
    public function getEmployee(): SoftDeleteQuery
    {
        return $this->hasOne($this->employeeClass, ['id' => 'employee_id']);
    }
}
