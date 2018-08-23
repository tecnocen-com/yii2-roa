<?php
namespace app\models;
/**
 * Model class for table `{{%safe_delete_child}}`
 *
 * @property integer $id
 * @property string $name
 *
 * @property SafeDelete $safeDelete
 */
class SafeDeleteChild extends \yii\db\ActiveRecord
{
    /**
     * @var string full class name of the model used in the relation
     * `getSafeDelete()`.
     */
    protected $safeDeleteClass = SafeDelete::class;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%safe_delete_child}}';
    }
    /**
     * @inheritdoc
     */
    protected function attributeTypecast()
    {
        return parent::attributeTypecast() + [
            'id' => 'integer',
            'safe_delete_id' => 'integer',
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['safe_delete_id'], 'required'],
            [
                ['safe_delete_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => SafeDelete::class,
                'targetAttribute' => ['safe_delete_id' => 'id'],
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge([
            'id' => 'ID',
            'safe_delete_id' => 'Safe Delete ID'
        ], parent::attributeLabels());
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSafeDelete()
    {
        return $this->hasOne(
            $this->safeDeleteClass,
            ['id' => 'safe_delete_id']
        );
    }
}
