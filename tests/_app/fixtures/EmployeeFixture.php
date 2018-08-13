<?php

namespace app\fixtures;

use app\models\Employee;

/**
 * Fixture to load default employees.
 */
class EmployeeFixture extends \yii\test\ActiveFixture
{

    /**
     * @inheritdoc
     */	
	public $depends = ['app\fixtures\ShopFixture'];

    /**
     * @inheritdoc
     */
    public $modelClass = Employee::class;

    /**
     * @inheritdoc
     */    
    public $dataFile = __DIR__ . '/data/employee.php';
}
