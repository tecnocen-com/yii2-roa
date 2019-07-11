<?php

namespace app\api\resources;

use app\api\models\{Employee, EmployeeSearch};
use app\models\SoftDeleteQuery;
use tecnocen\roa\{
    actions\SoftDelete as ActionSoftDelete,
    controllers\Resource
};
use yii\db\ActiveQuery;

/**
 * Resource to 
 */
class EmployeeResource extends Resource
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['delete']['class'] = ActionSoftDelete::class;

        return $actions;
    }

    /**
     * @inheritdoc
     */
    public $idAttribute = 'e.id';

    /**
     * @inheritdoc
     */
    public $modelClass = Employee::class;

    /**
     * @inheritdoc
     */
    public $searchClass = EmployeeSearch::class;

    /**
     * @inheritdoc
     */
    public $filterParams = ['shop_id'];

    /**
     * @inheritdoc
     */
    protected function baseQuery(): ActiveQuery
    {
        return parent::baseQuery()->andFilterDeleted('e')->innerJoinWith([
            'shop' => function (SoftDeleteQuery $query) {
                // only find if shop is not deleted.
                $query->andFilterDeleted('s');
            },
        ]);
    }
}
