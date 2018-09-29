<?php

namespace app\api\models;

use tecnocen\roa\hal\Contract;
use tecnocen\roa\hal\ContractTrait;

/**
 * ROA contract to handle shop employee records.
 */
class Employee extends \app\models\Employee implements Contract
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
        return ['shop'];
    }

    /**
     * @inheritdoc
     */
    protected function slugBehaviorConfig()
    {
        return [
            'resourceName' => 'employee',
            'parentSlugRelation' => 'shop',
        ];
    }
}
