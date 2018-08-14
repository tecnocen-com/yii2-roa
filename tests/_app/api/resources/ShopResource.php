<?php

namespace app\api\resources;

use app\api\models\Shop;
use app\api\models\ShopSearch;

/**
 * CRUD resource for `Shop` records
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class ShopResource extends \tecnocen\roa\controllers\Resource
{
    /**
     * @inheritdoc
     */
    public $modelClass = Shop::class;

    /**
     * @inheritdoc
     */
    public $searchClass = ShopSearch::class;
}
