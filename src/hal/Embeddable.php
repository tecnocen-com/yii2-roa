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
     * @return string property which will contain all the expanded parameters.
     */
    public function getExpandEnvelope();
}
