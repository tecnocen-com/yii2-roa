<?php

namespace app\api\resources;

use app\api\models\SaleItem;
use app\models\SoftDeleteQuery;
use yii\db\ActiveQuery;

/**
 * Resource to
 */
class SaleItemResource extends \tecnocen\roa\controllers\Resource
{
    /**
     * @inheritdoc
     */
    public $idAttribute = 'sale_item.item_id';

    /**
     * @inheritdoc
     */
    public $modelClass = SaleItem::class;

    /**
     * @inheritdoc
     */
    public $filterParams = ['sale_id', 'employee_id', 'shop_id', 'item_id'];

    /**
     * @inheritdoc
     */
    protected function baseQuery(): ActiveQuery
    {
        return parent::baseQuery()->alias('sale_item')->innerJoinWith([
            'item' => function (SoftDeleteQuery $query) {
                // only find if item is not deleted.
                $query->andFilterDeleted('item');
            },
            'sale' => function (SoftDeleteQuery $query) {
                // only find if sale is not deleted.
                $query->andFilterDeleted('sale');
            },
            'sale.employee' => function (SoftDeleteQuery $query) {
                // only find if employee is not deleted.
                $query->andFilterDeleted('employee');
            },
            'sale.employee.shop' => function (SoftDeleteQuery $query) {
                // only find if shop is not deleted.
                $query->andFilterDeleted('shop');
            },
        ]);
    }

}
