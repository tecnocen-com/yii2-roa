<?php

namespace app\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\db\ActiveQuery;

trait SoftDeleteTrait
{
    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'deleted' => true
                ],
            ],
        ];
    }

    public static function find()
    {
        return new SoftDeleteQuery(static::class);
    }
}
