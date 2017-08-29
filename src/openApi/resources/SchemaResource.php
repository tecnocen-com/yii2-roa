<?php

namespace tecnocen\roa\openApi\resources;

use Yii;

/**
 * Resource that shows the `schemas` property for an OpenApi 3.0 document.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class SchemaResource extends \yii\rest\Controller
{
    /**
     * @return array|GeneratorInterface[] generators to create the full schemas
     * property for an OpenApi 3.0 document.
     */
    public $generators = [];

    /**
     * Action that generates the full schemas property for OpenApi 3.0.
     *
     * @return array OpenApi3.0 object schema
     */
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
