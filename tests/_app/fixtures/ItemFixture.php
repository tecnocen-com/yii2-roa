<?php

namespace app\fixtures;

use app\models\Item;

/**
 * Fixture to load default items.
 */
class ItemFixture extends \yii\test\ActiveFixture
{
    /**
     * @inheritdoc
     */
    public $modelClass = Item::class;
    
    /**
     * @inheritdoc
     */    
    public $dataFile = __DIR__ . '/data/item.php';
}
