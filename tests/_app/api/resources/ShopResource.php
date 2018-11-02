<?php

namespace app\api\resources;

use tecnocen\roa\actions\SoftDelete as ActionSoftDelete;
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
    public function actions()
    {
        $actions = parent::actions();
        $actions['delete']['class'] = ActionSoftDelete::class;
        return $actions;
    }
    /**
     * @inheritdoc
     */
    public $modelClass = Shop::class;

    /**
     * @inheritdoc
     */
    public $searchClass = ShopSearch::class;
}
