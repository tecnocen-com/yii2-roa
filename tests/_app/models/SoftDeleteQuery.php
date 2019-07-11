<?php

namespace app\models;

use yii\db\ActiveQuery;

class SoftDeleteQuery extends ActiveQuery
{
    public function andFilterDeleted(
        string $alias = '',
        bool $isDeleted = false
    ): SoftDeleteQuery {
        if ($alias) {
            $this->alias($alias);
        } else {
            $alias = $this->getPrimaryTableName();
        }

        return $this->andWhere(["$alias.deleted" => $isDeleted]);
    }
}
