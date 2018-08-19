<?php

namespace app\fixtures;

use app\models\SafeDelete;

/**
 * Fixture to load default safe_delete.
 */
class SafeDeleteFixture extends \yii\test\ActiveFixture
{

    /**
     * @inheritdoc
     */
    public $modelClass = SafeDelete::class;

    /**
     * @inheritdoc
     */    
    public $dataFile = __DIR__ . '/data/safe_delete.php';
}
