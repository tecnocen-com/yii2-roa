<?php

namespace tecnocen\roa\hal;

use yii\web\Linkable;

/**
 * Interface which adds all the needed support for a HAL contract.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
interface Contract extends Embeddable, Linkable
{
    /**
     * @return string the URL to the record being referenced.
     */
    public function getSelfLink(): string;
}
