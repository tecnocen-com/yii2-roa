<?php

namespace app\api\resources;

use tecnocen\roa\actions\Restore;
use app\api\models\ShopRecovery;

/**
 * CRUD resource for `Shop` records
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class ShopRecoveryResource extends \tecnocen\roa\controllers\Resource
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['delete']['class'] = Restore::class;
        return $actions;
    }
    /**
     * @inheritdoc
     */
    public $modelClass = ShopRecovery::class;
}
