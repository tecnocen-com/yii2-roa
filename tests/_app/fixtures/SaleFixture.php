<?php

namespace app\fixtures;

use app\models\Sale;

/**
 * Fixture to load default sales.
 */
class SaleFixture extends \yii\test\ActiveFixture
{

    /**
     * @inheritdoc
     */	
	public $depends = ['app\fixtures\EmployeeFixture'];

    /**
     * @inheritdoc
     */
    public $modelClass = Sale::class;

    /**
     * @inheritdoc
     */    
    public $dataFile = __DIR__ . '/data/sale.php';
}
