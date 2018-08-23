<?php

namespace app\api\resources;

use app\api\models\SafeDelete;
use tecnocen\roa\actions\SafeDelete as ActionSafeDelete;

/**
 * CRUD resource for `SafeDelete` records
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class SafeDeleteResource extends \tecnocen\roa\controllers\Resource
{
    /**
     * @inheritdoc
     */
    public $modelClass = SafeDelete::class;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['delete']['class'] = ActionSafeDelete::class;

        return $actions;
    }
}
