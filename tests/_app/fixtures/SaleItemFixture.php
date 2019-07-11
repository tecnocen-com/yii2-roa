<?php

namespace app\fixtures;

use app\models\SaleItem;

/**
 * Fixture to load default sales.
 */
class SaleItemFixture extends \yii\test\ActiveFixture
{

    /**
     * @inheritdoc
     */	
	public $depends = ['app\fixtures\SaleFixture'];

    /**
     * @inheritdoc
     */
    public $modelClass = SaleItem::class;

    /**
     * @inheritdoc
     */    
    public $dataFile = __DIR__ . '/data/sale_item.php';
}
