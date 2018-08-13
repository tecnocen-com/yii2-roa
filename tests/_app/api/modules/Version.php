<?php

namespace app\api\modules;

use app\api\resources\ShopResource;
use app\api\resources\EmployeeResource;

class Version extends \tecnocen\roa\modules\ApiVersion
{
    const SHOP_ROUTE = 'shop';
    const EMPLOYEE_ROUTE = self::SHOP_ROUTE . '/<shop_id:\d+>/employee';

    /**
     * @inheritdoc
     */
    public $resources = [
        self::SHOP_ROUTE => ['class' => ShopResource::class],
        self::EMPLOYEE_ROUTE => ['class' => EmployeeResource::class],
    ];
}