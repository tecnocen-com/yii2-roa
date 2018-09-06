<?php

namespace app\fixtures;

use app\models\File;

/**
 * Fixture to load default files.
 */
class FileFixture extends \yii\test\ActiveFixture
{
    /**
     * @inheritdoc
     */
    public $modelClass = File::class;
    
    /**
     * @inheritdoc
     */    
    public $dataFile = __DIR__ . '/data/file.php';
}
