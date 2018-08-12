<?php

namespace app\fixtures;

use app\models\Employee;

class EmployeeFixture extends \yii\test\ActiveFixture
{
	public $depends = [ShopFixture::class];

    public $modelClass = Employee::class;
    
    public $dataFile = __DIR__ . '/data/employee.php';
}
