<?php

namespace tecnocen\roa\openApi\schema;

use tecnocen\roa\openApi\DataType;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Active Schema Generator based on an Active Record class which might have an
 * `AttributeTypecastBehavior` attached.
 *
 * @author Angel (Faryshta) Guevara <aguevara@solmipro.com>
 */
class ARGenerator extends \yii\base\Object implements
    GeneratorInterface
{
    /**
     * @var string class name extending `yii\base\BaseActiveRecord`
     */
    public $modelClass;

    /**
     * @var string name of the model to be used to create the schemas property
     * names. If not set then `BaseActiveRecord::formName()` will be used
     * instead.
     */
    public $modelName;

    /**
     * Template to generate the schemas property name for object.
     *
     * It can contain a placeholder '{model}' which will be replaced by the
     * value of `$modelName`.
     *
     * @var string
     */
    public $objectTemplate = '{model}';

    /**
     * Template to generate the schemas property name for object creation.
     *
     * It can contain a placeholder '{model}' which will be replaced by the
     * value of `$modelName`.
     *
     * @var string
     */
    public $objectCreateTemplate = '{model}.Create';

    /**
     * Template to generate the schemas property name for object collection.
     *
     * It can contain a placeholder '{model}' which will be replaced by the
     * value of `$modelName`.
     *
     * @var string
     */
    public $objectCollectionTemplate = '{model}.Collection';

    /**
     * @var string id to obtain an `AttributeTypecastBehavior` attached to the
     * model which will be used to determine each attribute type.
     */
    public $typecastBehaviorId = 'typecast';


    /**
     * @var scenario which will handle object creation.
     */
    public $createScenario = BaseActiveRecord::SCENARIO_DEFAULT;

    /**
     * @inheritdoc
     */
    public function generateSchemas()
    {
        $modelClass = $this->modelClass;
        /** @var BaseActiveRecord $model */
        $model = new $modelClass([
            'scenario' => $this->createScenario,
        ]);

        if (empty($this->modelName)) {
            $this->modelName = $model->formName();
        }

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

    /**
     * Name of the OpenApi Schemas property which will contain the schema
     * for object creation.
     *
     * @return string
     * @see $objectCreateTemplate
     */
    protected function objectCreateProperty()
    {
        return strtr($this->objectCreateTemplate, [
            '{model}' => $this->modelName,
        ]);
    }

    /**
     * Name of the OpenApi Schemas property which will contain the schema
     * for objects.
     *
     * @return string
     * @see $objectTemplate
     */
    protected function objectProperty()
    {
        return strtr($this->objectTemplate, [
            '{model}' => $this->modelName,
        ]);
    }

    /**
     * Name of the OpenApi Schemas property which will contain the schema
     * for collections.
     *
     * @return string
     * @see $objectCollectionTemplate
     */
    protected function objectCollectionProperty()
    {
        return strtr($this->objectCollectionTemplate, [
            '{model}' => $this->modelName,
        ]);
    }

    /**
     * Generates an schema to create new objects.
     *
     * @param  BaseActiveRecord $model active record which will generate the
     * schema.
     * @param string[] $attributeTypes pairs of attribute => typecast and its
     * respective typecast.
     * @param  array $safeAttributes [description]
     * @param  array $fields         [description]
     * @return array OpenApi3.0 object schema
     */
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

    /**
     * Generates the schema for the model objects.
     *
     * @param BaseActiveRecord $model model which will be used to generate
     * the labels for the attributes.
     * @param string[] $attributeTypes pairs of attribute => typecast and its
     * respective typecast.
     * @param string $unsafeAttributes list of attributes which are not
     * updatable
     * @return array OpenApi3.0 object schema
     */
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

    /**
     * Generates the schema for collection of objects.
     *
     * @return array OpenApi3.0 schema
     */
    protected function objectCollectionSchema()
    {
        return [
            'type' => DataType::TYPE_ARRAY,
            'items' => ['$ref' => '#' . $this->objectProperty()],
        ];
    }

    /**
     * Gets the pairs of attributes and their respective casting. Its based on
     * AttributeTypecastBehavior so if said behavior is not behing used this
     * method won't return anything unless its over-written.
     *
     * @param BaseActiveRecord $model
     * @return string[] pairs of attribute => typecast.
     */
    protected function attributeTypes(BaseActiveRecord $model)
    {
        if (null === (
            $typecastBehavior = $model->getBehavior($this->typecastBehaviorId)
        )) {
            return [];
        }
        if (!$typecastBehavior instanceof AttributeTypecastBehavior) {
            throw new InvalidArgumentException(
                "Behavior $this->typecastBehaviorID is not an instance of "
                    . AttributeTypecastBehavior::class
            );
        }

        return $typecastBehavior->attributeTypes;
    }
}
