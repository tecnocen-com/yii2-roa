<?php

namespace app\fixtures;

use app\models\Shop;

class ShopFixture extends \yii\test\ActiveFixture
{
    public $modelClass = Shop::class;
    
    public $dataFile = __DIR__ . '/data/shop.php';
}
