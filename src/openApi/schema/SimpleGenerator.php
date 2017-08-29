<?php

namespace tecnocen\roa\openApi\schema;

/**
 * Simple OpenApi 3.0 schemas generator which returns the value of `$schemas`
 */
class SimpleGenerator extends \yii\base\Object implements
    GeneratorInterface
{
    /**
     * @return array OpenApi3.0 object schema
     */
    public $schemas;

    /**
     * @inheritdoc
     */
    public function generateSchemas()
    {
        return $this->schemas;
    }
}
