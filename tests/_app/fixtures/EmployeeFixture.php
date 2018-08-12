<?php

namespace app\fixtures;

use app\models\Employee;

class EmployeeFixture extends \yii\test\ActiveFixture
{
	public $depends = ['app\fixtures\ShopFixture'];

    public $modelClass = Employee::class;
    
    public $dataFile = __DIR__ . '/data/employee.php';
}
