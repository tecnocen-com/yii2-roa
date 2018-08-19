<?php

namespace app\fixtures;

use app\models\SoftDelete;

/**
 * Fixture to load default employees.
 */
class SoftDeleteFixture extends \yii\test\ActiveFixture
{

    /**
     * @inheritdoc
     */
    public $modelClass = SoftDelete::class;

    /**
     * @inheritdoc
     */    
    public $dataFile = __DIR__ . '/data/soft_delete.php';
}
