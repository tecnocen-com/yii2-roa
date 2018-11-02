<?php

namespace app\api\resources;

use Yii;
use yii\web\NotFoundHttpException;
use tecnocen\roa\actions\SafeDelete as ActionSafeDelete;
use app\api\models\Item;
/**
 * Resource to
 */
class ItemResource extends \tecnocen\roa\controllers\Resource
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['delete']['class'] = ActionSafeDelete::class;
        return $actions;
    }
    /**
     * @inheritdoc
     */
    public $modelClass = Item::class;

}
