<?php

namespace app\api\resources;

use Yii;
use yii\web\NotFoundHttpException;
use tecnocen\roa\actions\SafeDelete as ActionSafeDelete;
use app\api\models\Sale;
use app\api\models\SaleSearch;
/**
 * Resource to
 */
class SaleResource extends \tecnocen\roa\controllers\Resource
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
    public $modelClass = Sale::class;

    /**
     * @inheritdoc
     */
    public $searchClass = SaleSearch::class;

}
