<?php

namespace app\api\resources;

use Yii;
use yii\web\NotFoundHttpException;
use tecnocen\roa\actions\Delete as ActionDelete;
use app\api\models\SaleItem;
/**
 * Resource to
 */
class SaleItemResource extends \tecnocen\roa\controllers\Resource
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['delete']['class'] = ActionDelete::class;
        return $actions;
    }
    /**
     * @inheritdoc
     */
    public $modelClass = SaleItem::class;

}
