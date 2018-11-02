<?php

namespace app\api\models;

use tecnocen\roa\hal\Contract;
use tecnocen\roa\hal\ContractTrait;

/**
 * ROA contract to handle shop sale records.
 */
class Sale extends \app\models\Sale implements Contract
{
    use ContractTrait;

    /**
     * @inheritdoc
     */
    protected $shopClass = Shop::class;

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return [
            'shop',
            'employee'
        ];
    }

    /**
     * @inheritdoc
     */
    protected function slugBehaviorConfig(): array
    {
        return [
            'resourceName' => 'sale',
            'parentSlugRelation' => 'shop',
        ];
    }
}
