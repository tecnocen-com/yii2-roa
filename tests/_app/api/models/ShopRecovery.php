<?php

namespace app\api\models;

use tecnocen\roa\hal\Contract;
use tecnocen\roa\hal\ContractTrait;
use yii\web\NotFoundHttpException;

/**
 * ROA contract to handle shop records.
 */
class ShopRecovery extends \app\models\ShopRecovery implements Contract
{
    use ContractTrait {
        getLinks as getContractLinks;
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

    /**
     * @inheritdoc
     */
    protected function slugBehaviorConfig(): array
    {
        return [
            'resourceName' => 'shop',
        ];
    }
}
