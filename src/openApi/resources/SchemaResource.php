<?php

namespace tecnocen\roa\openApi\resources;

use tecnocen\roa\openApi\DataType;
use Yii;

/**
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class SchemaResource extends \yii\rest\Controller
{
    public $generators = [];
    public function actionIndex()
    {
        $schemas = [];
        foreach ($this->generators as $generator) {
            $generator = Yii::createObject($generator);
            $schemas = array_merge($schemas, $generator->generateSchemas());
        }
        return $schemas;
    }
}
