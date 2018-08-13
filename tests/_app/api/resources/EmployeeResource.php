<?php

namespace app\api\resources;

use Yii;
use yii\web\NotFoundHttpException;
use app\api\models\Employee;
use app\api\models\EmployeeSearch;
/**
 * Resource to
 */
class EmployeeResource extends \tecnocen\roa\controllers\OAuth2Resource
{
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