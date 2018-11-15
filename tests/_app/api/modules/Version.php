<?php

namespace app\api\modules;

use app\api\resources\{
    EmployeeResource,
    EmployeeRestoreResource,
    ItemResource,
    ItemRestoreResource,
    SaleResource,
    SaleItemResource,
    ShopRestoreResource,
    ShopResource
};
use tecnocen\roa\{
    controllers\ProfileResource,
    modules\ApiVersion,
    urlRules\Profile as ProfileUrlRule,
    urlRules\File as FileUrlRule
};

class Version extends ApiVersion
{
    public $releaseDate = '2018-06-15';
    public $deprecationDate = '2020-01-01';
    public $obsoleteDate = '2020-12-31';

    const ITEM_ROUTE = 'item';
    const ITEM_RESTORE_ROUTE = 'item-restore';

    const SHOP_ROUTE = 'shop';
    const SHOP_RESTORE_ROUTE = 'shop-restore';

    const EMPLOYEE_ROUTE = self::SHOP_ROUTE . '/<shop_id:\d+>/employee';
    const EMPLOYEE_RESTORE_ROUTE = self::SHOP_ROUTE
        . '/<shop_id:\d+>/employee-restore';

    const SALE_ROUTE = self::EMPLOYEE_ROUTE . '/<employee_id:\d+>/sale';
    const SALE_RESTORE_ROUTE = self::EMPLOYEE_ROUTE
        . '/<employee_id:\d+>/sale-recovery';

    const SALE_ITEM_ROUTE = self::SALE_ROUTE . '/<sale_id:\d+>/item';

    /**
     * @inheritdoc
     */
    public $resources = [
        'profile' => [
            'class' => ProfileResource::class,
            'urlRule' => ['class' => ProfileUrlRule::class],
        ],
        'file' => [
            'class' => FileResource::class,
            'urlRule' => [
                'class' => FileUrlRule::class,
            ],
        ],

        self::ITEM_ROUTE => ['class' => ItemResource::class],
        self::ITEM_RESTORE_ROUTE => ['class' => ItemRestoreResource::class],

        self::SHOP_ROUTE => ['class' => ShopResource::class],
        self::SHOP_RESTORE_ROUTE => ['class' => ShopRestoreResource::class],

        self::EMPLOYEE_ROUTE => ['class' => EmployeeResource::class],
        self::EMPLOYEE_RESTORE_ROUTE => [
            'class' => EmployeeRestoreResource::class,
        ],

        self::SALE_ROUTE => ['class' => SaleResource::class],
        self::SALE_RESTORE_ROUTE => ['class' => SaleRestoreResource::class],

        self::SALE_ITEM_ROUTE => ['class' => SaleItemResource::class],
    ];

    public $apidoc = 'http://mockapi.com/v1';
}
