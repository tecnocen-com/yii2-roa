<?php

namespace app\fixtures;

use app\models\SoftDelete;

/**
 * Fixture to load default employees.
 */
class RestoreSoftDeleteFixture extends \yii\test\ActiveFixture
{

    /**
     * @inheritdoc
     */
    public $modelClass = SoftDelete::class;

    /**
     * @inheritdoc
     */    
    public $dataFile = __DIR__ . '/data/restore_soft_delete.php';
}
