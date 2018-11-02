<?php

namespace app\api\resources;

use Yii;
use yii\web\NotFoundHttpException;
use tecnocen\roa\actions\SoftDelete as ActionSoftDelete;
use app\api\models\Employee;
use app\api\models\EmployeeSearch;
/**
 * Resource to
 */
class EmployeeResource extends \tecnocen\roa\controllers\Resource
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
    public $modelClass = Employee::class;

    /**
     * @inheritdoc
     */
    public $searchClass = EmployeeSearch::class;

    /**
     * @inheritdoc
     */
    public $filterParams = ['shop_id'];
}
