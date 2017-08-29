<?php

namespace tecnocen\roa\openApi\schema;

use tecnocen\roa\openApi\DataType;

/**
 * Generates the default error schemas.
 */
class ErrorStatusGenerator extends \yii\base\Object implements
    GeneratorInterface
{
    /**
     * @inheritdoc
     */
    public function generateSchemas()
    {
        return [
            'UnprocessableEntity' => [
                'type' => DataType::TYPE_ARRAY,
                'items' => [
                    '$ref' => '#components/schema/ValidationError',
                ],
            ],
            'ValidationError' => [
                'properties' => [
                    'type' => DataType::TYPE_OBJECT,
                    'field' => [
                        'type' => DataType::TYPE_STRING,
                    ],
                    'message' => [
                        'type' => DataType::TYPE_STRING,
                    ],
                ],
            ],
        ];
    }
}
