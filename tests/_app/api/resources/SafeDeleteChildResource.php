<?php

namespace app\api\resources;

use Yii;
use yii\web\NotFoundHttpException;
use app\api\models\SafeDeleteChild;
/**
 * Resource to
 */
class SafeDeleteChildResource extends \tecnocen\roa\controllers\Resource
{
    /**
     * @inheritdoc
     */
    public $modelClass = SafeDeleteChild::class;

    /**
     * @inheritdoc
     */
    public $filterParams = ['safe_delete_id'];
}
