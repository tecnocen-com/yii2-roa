<?php

namespace app\api\resources;

use app\api\models\Shop;

/**
 * CRUD resource for `Shop` records
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class ShopResource extends \tecnocen\roa\controllers\OAuth2Resource
{
    /**
     * @inheritdoc
     */
    public $modelClass = Shop::class;

}