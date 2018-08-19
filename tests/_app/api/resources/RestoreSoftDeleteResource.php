<?php

namespace app\api\resources;

use app\api\models\RestoreSoftDelete;
use tecnocen\roa\actions\Restore;

/**
 * CRUD resource for `RestoreSoftDelete` records
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class RestoreSoftDeleteResource extends \tecnocen\roa\controllers\Resource
{
    /**
     * @inheritdoc
     */
    public $modelClass = RestoreSoftDelete::class;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['delete']['class'] = Restore::class;

        return $actions;
    }
}
