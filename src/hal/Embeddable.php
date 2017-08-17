<?php

namespace tecnocen\roa\hal;

/**
 * Interface to get a the information of a file associated to a model.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
interface Embeddable extends \yii\base\Arrayable
{
    const LINKS_PROPERTY = '_links';
    const EMBEDDED_PROPERTY = '_embedded';

    /**
     * Converts the model into an array.
     *
     * @param array $fields the fields being requested. If empty, all fields as
     * specified by [[fields()]] will be returned.
     * @param array $expand the additional fields being requested for exporting.
     * Only fields declared in [[extraFields()]] will be considered.
     * @param bool $recursive whether to recursively return array representation
     * of embedded objects.
     * @return array the array representation of the object
     */
    public function toArray(
        array $fields = [],
        array $expand = [],
        $recursive = true
    );

    /**
     * @return string property which will contain all the expanded parameters.
     */
    public function getExpandEnvelope();
}
