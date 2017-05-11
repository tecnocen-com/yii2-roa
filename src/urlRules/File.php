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
        'GET,HEAD' => 'view',
        '' => 'options',
    ];

    /**
     * @var string[] list of valid extensions that this rule can handle.
     */
    public $extensions = ['png', 'jpg'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->tokens = [
            '{id}' => '<id:\\d+>',
            '{ext}' => '<ext:[' . implode($this->extensions, '|') . ']>',
        ];
        parent::init();
    }
}
