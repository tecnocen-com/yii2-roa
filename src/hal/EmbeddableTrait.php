<?php

namespace tecnocen\roa\hal;

use yii\base\Arrayable;
use yii\web\Link;
use yii\web\Linkable;

/**
 * Interface to get a the information of a file associated to a model.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
trait EmbeddableTrait
{
    /**
     * Converts the model into an array.
     *
     * This method will first identify which fields to be included in the
     * resulting array by calling [[fields()]] and [[extraFields()]].
     * If the model implements the [[Linkable]] interface, the resulting array will also have a `_link` element
     * which refers to a list of links as specified by the interface.
     *
     * @param array $fields the fields being requested. If empty, all fields as
     * specified by [[fields()]] will be returned.
     * @param array $expand the additional fields being requested for exporting.
     * Only fields declared in [[extraFields()]] will be considered.
     * @param bool $recursive whether to recursively return array representation
     * of embedded objects.
     * @param string $expandEnvelope array key which will contain properties
     * declared in `$expand`.
     * @return array the array representation of the object
     */
    public function toArray(
        array $fields = [],
        array $expand = [],
        $recursive = true
    ) {
        $data = [];
        $parsedExpand = [];

        // resolve fields
        foreach ($this->fields() as $field => $definition) {
            if (is_int($field)) {
                $field = $definition;
            }
            if (empty($fields) || in_array($field, $fields, true)) {
                $data[$field] = $this->processField($field, $definition);
            }
        }

        // resolve expand
        foreach ($expand as $property) {
            $propertyBreak = explode('.', $property, 2);
            $propertyName = $propertyBreak[0];
            if (!isset($parsedExpand[$propertyName])) {
                $parsedExpand[$propertyName] = [];
            }
            if ($recursive && isset($propertyBreak[1])) {
                $parsedExpand[$propertyName][] = $propertyBreak[1];
            }
        }
        foreach ($this->extraFields() as $field => $definition) {
            if (is_int($field)) {
                $field = $definition;
            }
            if (isset($parsedExpand[$field])) {
                $fieldData = $this->processField($field, $definition);
                if ($fieldData instanceof Arrayable) {
                    // resolve expand from expanded property when `$recursive`
                    $fieldData = $fieldData->toArray(
                        [],
                        $parsedExpand[$field],
                        $recursive
                    );
                }
                if ($expandEnvelope = $this->getExpandEnvelope()) {
                    // store based on $expandEnvelope
                    $data[$expandEnvelope][$field] = $fieldData;
                } else {
                    $data[$field] = $fieldData;
                }
            }
        }

        if ($this instanceof Linkable) {
            $data[Embeddable::LINKS_PROPERTY] = Link::serialize(
                $this->getLinks()
            );
        }

        return $data;
    }

    /**
     * @return string property which will contain all the expanded parameters.
     */
    public function getExpandEnvelope()
    {
        return Embeddable::EMBEDDED_PROPERTY;
    }

    /**
     * @param string $field name of the field to be resolved.
     * @param string|callable $definition the field definition, it its an string
     * it will be used as a property name, or a callable with signature.
     *
     * ```php
     * function ($model, string $field)
     * ```
     * @return mixed data obtained from the model.
     */
    protected function processField($field, $definition)
    {
        return is_string($definition)
            ? $this->$definition
            : call_user_func($definition, $this, $field);
    }
}
