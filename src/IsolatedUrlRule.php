<?php

namespace tecnocen\roa;

use Yii;


/**
 * Rule for routing resources which have an isolated record, that means the
 * resource doesn't handle collections.
 */
class IsolatedUrlRule extends UrlRule
{
    /**
     * @inheritdoc
     */
    public $pluralize = false;

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
