<?php

namespace app\api\models;

use tecnocen\roa\hal\Contract;
use tecnocen\roa\hal\ContractTrait;
use yii\web\NotFoundHttpException;

/**
 * ROA contract to handle shop records.
 */
class Shop extends \app\models\Shop implements Contract
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
            'resourceName' => 'shop',
            'checkAccess' => function ($params) {
                if (isset($params['shop_id'])
                    && $this->id != $params['shop_id']
                ) {
                    throw new NotFoundHttpException(
                       'Shop not associated to element.'
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
            'employee' => $this->getSelfLink() . '/employee',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['employees'];
    }
}
