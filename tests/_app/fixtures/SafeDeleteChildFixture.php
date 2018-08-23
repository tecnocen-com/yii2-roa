<?php

namespace app\fixtures;

use app\models\SafeDeleteChild;

/**
 * Fixture to load default safe_delete_child.
 */
class SafeDeleteChildFixture extends \yii\test\ActiveFixture
{

    /**
     * @inheritdoc
     */	
    public $depends = ['app\fixtures\SafeDeleteFixture'];
    /**
     * @inheritdoc
     */
    public $modelClass = SafeDeleteChild::class;

    /**
     * @inheritdoc
     */    
    public $dataFile = __DIR__ . '/data/safe_delete_child.php';
}
