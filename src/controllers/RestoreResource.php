<?php

namespace tecnocen\roa\controllers;

use tecnocen\roa\actions\Restore;

/**
 * Resource to handle restoration of soft deleted records.
 *
 * ```php
 * class ShopRestoreResource extends RestoreRestource
 * {
 *     public function baseQuery(): ActiveQuery
 *     {
 *         returns Shop::find()->andWhere(['isDeleted' => false]);
 *     }
 * }
 * ```
 *
 * The verb GET works on both collections and records to give reading access.
 *
 * By default the verb DELETE is enabled and its meant to permanently delete
 * a record but can be disabled by unsetting the verb.
 *
 * The verbs POST, PATCH and PUT work only on records to restore them.
 */
class RestoreResource extends Resource
{
    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        $verbs = parent::verbs();
        unset($verbs['create']);

        return $verbs;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        $actions['update'] = [
            'class' => Restore::class,
            'modelClass' => $this->modelClass,
            'findModel' => [$this, 'findModel'],
        ];

        return $actions;
    }
}
