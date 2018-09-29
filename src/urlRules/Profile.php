<?php

namespace tecnocen\roa\urlRules;

/**
 * Url Rule intended solely for the a resource which returns the authenticated
 * user profile.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 * @see \tecnocen\roa\controllers\ProfileResource
 */
class Profile extends Resource
{
    /**
     * @inheritdoc
     */
    public $patterns = [
        'PUT,PATCH' => 'update',
        'GET,HEAD' => 'view',
        '' => 'options',
    ];

    /**
     * @inheritdoc
     */
    public $tokens = [];
}
