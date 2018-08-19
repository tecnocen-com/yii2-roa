<?php

namespace app\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * Model class for table `{{%soft_delete}}`
 *
 * @property integer $id
 *
 */
class RestoreSoftDelete extends SoftDelete
{
    /**
     * @inheritdoc
     */
    public static function find()
    {
        return parent::find()->where(['deleted' => true]);
    }
}
