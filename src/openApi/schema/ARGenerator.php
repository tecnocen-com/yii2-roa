<?php

namespace tecnocen\roa\openApi\schema;

use tecnocen\roa\openApi\DataType;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

class ARGenerator extends \yii\base\Object implements
    GeneratorInterface
{
    public $modelClass;
    public $modelName;
    public $objectTemplate = '{model}';
    public $objectCreateTemplate = '{model}.Create';
    public $objectCollectionTemplate = '{model}.Collection';
    public $typecastBehaviorId = 'typecast';
    public $createScenario = BaseActiveRecord::SCENARIO_DEFAULT;

    public function generateSchemas()
    {
        $modelClass = $this->modelClass;
        /** @var BaseActiveRecord $model */
        $model = new $modelClass([
            'scenario' => $this->createScenario,
        ]);

        $attributes = $model->attributes();
        $safeAttributes = $model->safeAttributes();
        $fields = $model->fields() ?: $attributes;
        $properties = [];
        $attributeTypes = $this->attributeTypes($model);

        return [
            $this->objectCreateProperty() => $this->objectCreateSchema(
                $model,
                $attributeTypes,
                $safeAttributes,
                $fields
            ),
            $this->objectProperty() => $this->objectSchema(
                $model,
                $attributeTypes,
                array_diff($fields, $safeAttributes)
            ),
            $this->objectCollectionProperty() => $this->objectCollectionSchema(),
        ];
    }

    protected function objectCreateProperty()
    {
        return strtr($this->objectCreateTemplate, [
            '{model}' => $this->modelName,
        ]);
    }

    protected function objectProperty()
    {
        return strtr($this->objectTemplate, [
            '{model}' => $this->modelName,
        ]);
    }

    protected function objectCollectionProperty()
    {
        return strtr($this->objectCollectionTemplate, [
            '{model}' => $this->modelName,
        ]);
    }

    protected function objectCreateSchema(
        BaseActiveRecord $model,
        array $attributeTypes,
        array $safeAttributes,
        array $fields
    ) {
        $properties = [];
        $requiredAttributes = [];
        foreach ($safeAttributes as $attribute) {
            if ($required = $model->isAttributeRequired($attribute)) {
                $requiredAttributes[] = $attribute;
            }
            $properties[$attribute] = [
                'title' => $model->getAttributeLabel($attribute),
                'type' => ArrayHelper::getValue(
                    $attributeTypes,
                    $attribute,
                    DataType::TYPE_STRING
                ),
                'write-only' => !in_array($attribute, $fields, true),
            ];
        }

        return [
            'type' => DataType::TYPE_OBJECT,
            'required' => $requiredAttributes,
            'properties' => $properties,
        ];
    }

    protected function objectSchema(
        BaseActiveRecord $model,
        array $attributeTypes,
        array $unsafeAttributes
    ) {
        $properties = [];
        foreach ($unsafeAttributes as $attribute) {
            $properties[$attribute] = [
                'title' => $model->getAttributeLabel($attribute),
                'type' => ArrayHelper::getValue(
                    $attributeTypes,
                    $attribute,
                    DataType::TYPE_STRING
                ),
                'read-only' => true,
            ];
        }

        return [
            'type' => DataType::TYPE_OBJECT,
            'allOf' => [
                ['$ref' => '#' . $this->objectCreatePropERTY()],
                [
                    'type' => DataType::TYPE_OBJECT,
                    'properties' => $properties,
                ],
            ],
        ];
    }

    protected function objectCollectionSchema()
    {
        return [
            'type' => DataType::TYPE_ARRAY,
            'items' => ['$ref' => '#'. $this->objectProperty()],
        ];
    }

    protected function attributeTypes($model)
    {
        if (null === (
            $typecastBehavior = $model->getBehavior($this->typecastBehaviorId)
        )) {
            return [];
        }

        return $typecastBehavior->attributeTypes;
    }
}
