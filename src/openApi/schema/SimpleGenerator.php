<?php

namespace tecnocen\roa\openApi\schema;

class SimpleGenerator extends \yii\base\Object implements
    GeneratorInterface
{
    public $schemas;

    public function generateSchemas()
    {
        return $this->schemas;
    }
}
