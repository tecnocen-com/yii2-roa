<?php

namespace tecnocen\roa\urlRules;

/**
 * Rule for routing response streams to access a file for download or view it
 * on browser.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class File extends Resource
{
    /**
     * @inheritdoc
     */
    public $patterns = [
        'PUT,PATCH {id}' => 'update',
        'DELETE {id}' => 'delete',
        'GET,HEAD {id}' => 'view',
        'POST' => 'create',
        'GET,HEAD' => 'index',
        '{id}' => 'options',
        'GET {id}.{ext}' => 'file-stream',
        '' => 'options',
    ];

    /**
     * @var string[] list of valid extensions that this rule can handle.
     */
    public $ext = ['png', 'jpg'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->tokens['{ext}'] = '<ext:(' . implode($this->ext, '|') . ')>';
        parent::init();
    }
}
