<?php

namespace app\api\models;

use tecnocen\roa\hal\Contract;
use tecnocen\roa\hal\ContractTrait;
use yii\web\NotFoundHttpException;

/**
 * ROA contract to handle item records.
 */
class Item extends \app\models\Item implements Contract
{
    use ContractTrait {
        getLinks as getContractLinks;
    }

    /**
     * @inheritdoc
     */
    protected $employeeClass = Employee::class;

    /**
     * @inheritdoc
     */
    protected function slugBehaviorConfig(): array
    {
        return [
            'resourceName' => 'item',
            'checkAccess' => function ($params) {
                if (isset($params['item_id'])
                    && $this->id != $params['item_id']
                ) {
                    throw new NotFoundHttpException(
                       'Item not associated to element.'
                    );
                }
            },
        ];
    }

    /**
     * @inheritdoc
     */
    public function getLinks()
    {
        return array_merge($this->getContractLinks(), [
            'sale' => $this->getSelfLink() . '/sale',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['sale'];
    }
}
