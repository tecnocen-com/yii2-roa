<?php

namespace app\api\modules;

use app\api\resources\EmployeeResource;
use app\api\resources\ItemResource;
use app\api\resources\SaleResource;
use app\api\resources\SaleItemResource;
use app\api\resources\ShopResource;
use app\api\resources\ShopRecoveryResource;
use app\api\resources\RestoreSoftDeleteResource;
use tecnocen\roa\controllers\ProfileResource;
use tecnocen\roa\urlRules\Profile as ProfileUrlRule;
use tecnocen\roa\urlRules\File as FileUrlRule;

class Version extends \tecnocen\roa\modules\ApiVersion
{

    public $releaseDate = '2018-06-15';
    public $deprecationDate = '2020-01-01';
    public $obsoleteDate = '2020-12-31';

    const ITEM_ROUTE = 'item';
    const SHOP_ROUTE = 'shop';
    const SHOP_RECOVERY_ROUTE = 'shop-recovery';
    const EMPLOYEE_ROUTE = self::SHOP_ROUTE . '/<shop_id:\d+>/employee';
    const SALE_ROUTE = self::SHOP_ROUTE . '/<shop_id:\d+>/sale';
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
        self::SHOP_ROUTE => ['class' => ShopResource::class],
        self::SHOP_RECOVERY_ROUTE => ['class' => ShopRecoveryResource::class],
        self::EMPLOYEE_ROUTE => ['class' => EmployeeResource::class],
        self::SALE_ROUTE => ['class' => SaleResource::class],
        self::SALE_ITEM_ROUTE => ['class' => SaleItemResource::class],
    ];

    public $apidoc = 'http://mockapi.com/v1';
}
