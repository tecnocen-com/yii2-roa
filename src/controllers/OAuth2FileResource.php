<?php

namespace tecnocen\roa\controllers;

use Yii;

/**
 * Resource which enables file upload to be stored and associated to a record.
 *
 * @author  Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class OAuth2FileResource extends OAuth2Resource
{
    /**
     * @var string[] array used in `tecnocen\roa\actions\Create::fileAttributes`
     * @see \tecnocen\roa\actions\LoadFileTrait::$fileAttributes
     */
    public $createFileAttributes = [];

    /**
     * @var string[] array used in `tecnocen\roa\actions\Update::fileAttributes`
     * @see \tecnocen\roa\actions\LoadFileTrait::$fileAttributes
     */
    public $updateFileAttributes = [];

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['create']['fileAttributes'] = $this->createFileAttributes;
        $actions['update']['fileAttributes'] = $this->updateFileAttributes;
        return $actions;
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['update'][] = 'POST';
        return $verbs;
    }
}
