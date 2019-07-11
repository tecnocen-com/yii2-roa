<?php

namespace app\api\resources;

use app\api\models\Item;
use tecnocen\roa\{
    actions\SafeDelete as ActionSafeDelete,
    controllers\Resource
};
use yii\db\ActiveQuery;

/**
 * Resource to
 */
class ItemResource extends Resource
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['delete']['class'] = ActionSafeDelete::class;

        return $actions;
    }

    /**
     * @inheritdoc
     */
    public $modelClass = Item::class;

    /**
     * @inheritdoc
     */
    protected function baseQuery(): ActiveQuery
    {
        return parent::baseQuery()->andFilterDeleted();
    }
}
