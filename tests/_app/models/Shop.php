<?php

namespace app\models;

/**
 * Model class for table `{{%shop}}`
 *
 * @property integer $id
 * @property string $name
 *
 * @property Employee[] $employee
 */
class Shop extends \tecnocen\rmdb\models\Entity
{
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
        return '{{%shop}}';
    }
    /**
     * @inheritdoc
     */
    protected function attributeTypecast()
    {
        return parent::attributeTypecast() + ['id' => 'integer'];
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
        return array_merge([
            'id' => 'ID',
            'name' => 'Shop Name',
        ], parent::attributeLabels());
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployees()
    {
        return $this->hasMany($this->employeeClass, ['shop_id' => 'id'])
            ->inverseOf('shop');
    }
}