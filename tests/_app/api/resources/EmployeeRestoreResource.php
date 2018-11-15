<?php

namespace app\api\resources;

use app\api\models\Employee;
use app\models\SoftDeleteQuery;
use tecnocen\roa\controllers\RestoreResource;
use yii\db\ActiveQuery;

/**
 * Resource to 
 */
class EmployeeRestoreResource extends RestoreResource
{
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
    public $filterParams = ['shop_id'];

    /**
     * @inheritdoc
     */
    protected function baseQuery(): ActiveQuery
    {
        return parent::baseQuery()
            ->andFilterDeleted('e', false)
            ->innerJoinWith([
                'shop' => function (SoftDeleteQuery $query) {
                    // only find if shop is not deleted.
                    $query->andFilterDeleted('s');
                },
            ]);
    }
}
