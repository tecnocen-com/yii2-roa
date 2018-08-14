<?php

namespace app\fixtures;

use app\models\Shop;

/**
 * Fixture to load default shops.
 */
class ShopFixture extends \yii\test\ActiveFixture
{
    /**
     * @inheritdoc
     */
    public $modelClass = Shop::class;
    
    /**
     * @inheritdoc
     */    
    public $dataFile = __DIR__ . '/data/shop.php';
}
