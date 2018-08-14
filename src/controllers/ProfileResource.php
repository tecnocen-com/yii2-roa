<?php

namespace tecnocen\roa\controllers;

use tecnocen\roa\actions\ProfileUpdate;
use tecnocen\roa\actions\ProfileView;

class ProfileResource extends Resource
{
    /**
     * @inheridoc
     */
    public function init()
    {
    }

    /**
     * @inheridoc
     */
    final public function verbs()
    {
        $verbs = parent::verbs();
        unset($verbs['index'], $verbs['create'], $verbs['delete']);

        return $verbs;
    }

    /**
     * @inheridoc
     */
    public function actions()
    {
        return [
            'view' => ['class' => ProfileView::class],
            'update' => ['class' => ProfileUpdate::class],
        ];
    }
}
