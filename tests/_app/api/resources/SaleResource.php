<?php

namespace app\api\resources;

use app\api\models\{Sale, SaleSearch};
use app\models\SoftDeleteQuery;
use tecnocen\roa\actions\SafeDelete as ActionSafeDelete;
use yii\db\ActiveQuery;

/**
 * Resource to
 */
class SaleResource extends \tecnocen\roa\controllers\Resource
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
    public $idAttribute = 'sale.id';

    /**
     * @inheritdoc
     */
    public $modelClass = Sale::class;

    /**
     * @inheritdoc
     */
    public $searchClass = SaleSearch::class;

    /**
     * @inheritdoc
     */
    protected function baseQuery(): ActiveQuery
    {
        return parent::baseQuery()->andFilterDeleted('sale')->innerJoinWith([
                'employee' => function (SoftDeleteQuery $query) {
                    // only active employees
                    $query->andFilterDeleted('employee');
                },
                'employee.shop' => function (SoftDeleteQuery $query) {
                    // only active shops
                    $query->andFilterDeleted('shop');
                },
            ]);
    }
}
