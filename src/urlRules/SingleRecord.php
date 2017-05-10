<?php

namespace tecnocen\roa\urlRules;

/**
 * Rule for routing resources which will only handle a record per authorized
 * user or globally in the sistem.
 *
 * That means the resource doesn't contain collections.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class SingleRecord extends Resource
{
    /**
     * @inheritdoc
     */
    public $patterns = [
        'PUT,PATCH' => 'update',
        'DELETE' => 'delete',
        'GET,HEAD' => 'view',
        'POST' => 'create',
        '' => 'options',
    ];

    /**
     * @inheritdoc
     */
    public $tokens = [];
}
