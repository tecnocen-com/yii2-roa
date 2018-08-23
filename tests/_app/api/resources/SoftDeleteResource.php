<?php

namespace app\api\resources;

use app\api\models\SoftDelete;
use tecnocen\roa\actions\SoftDelete as ActionSoftDelete;

/**
 * CRUD resource for `SoftDelete` records
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class SoftDeleteResource extends \tecnocen\roa\controllers\Resource
{
    /**
     * @inheritdoc
     */
    public $modelClass = SoftDelete::class;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['delete']['class'] = ActionSoftDelete::class;

        return $actions;
    }
}
