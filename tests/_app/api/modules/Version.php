<?php

namespace app\api\modules;

use app\api\resources\ShopResource;
use app\api\resources\EmployeeResource;
use app\api\resources\FileResource;
use app\api\resources\RestoreSoftDeleteResource;
use app\api\resources\SoftDeleteResource;
use app\api\resources\SafeDeleteResource;
use app\api\resources\SafeDeleteChildResource;
use tecnocen\roa\controllers\ProfileResource;
use tecnocen\roa\urlRules\SingleRecord;
use tecnocen\roa\urlRules\File as FileUrlRule;

class Version extends \tecnocen\roa\modules\ApiVersion
{

    public $releaseDate = '2018-06-15';
    public $deprecationDate = '2020-01-01';
    public $obsoleteDate = '2020-12-31';

    const SHOP_ROUTE = 'shop';
    const EMPLOYEE_ROUTE = self::SHOP_ROUTE . '/<shop_id:\d+>/employee';
    const RESTORE_SOFT_DELETE_ROUTE = 'restore-soft-delete';
    const SOFT_DELETE_ROUTE = 'soft-delete';
    const SAFE_DELETE_ROUTE = 'safe-delete';
    const SAFE_DELETE_CHILD_ROUTE = self::SAFE_DELETE_ROUTE . '/<safe_delete_id:\d+>/child';

    /**
     * @inheritdoc
     */
    public $resources = [
        'profile' => [
            'class' => ProfileResource::class,
            'urlRule' => ['class' => SingleRecord::class],
        ],
        'file' => [
            'class' => FileResource::class,
            'urlRule' => [
                'class' => FileUrlRule::class,
            ],
        ],
        self::SHOP_ROUTE => ['class' => ShopResource::class],
        self::EMPLOYEE_ROUTE => ['class' => EmployeeResource::class],
        self::RESTORE_SOFT_DELETE_ROUTE => ['class' => RestoreSoftDeleteResource::class],
        self::SOFT_DELETE_ROUTE => ['class' => SoftDeleteResource::class],
        self::SAFE_DELETE_ROUTE => ['class' => SafeDeleteResource::class],
        self::SAFE_DELETE_CHILD_ROUTE => ['class' => SafeDeleteChildResource::class],
    ];

    public $apidoc = 'http://mockapi.com/v1';
}
