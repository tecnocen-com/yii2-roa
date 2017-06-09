<?php

namespace tecnocen\roa\controllers;

use Yii;
use tecnocen\roa\actions\ProfileView;
use tecnocen\roa\actions\ProfileUpdate;
use tecnocen\roa\actions\CambiaPassword;
use yii\helpers\ArrayHelper;

class ProfileResource extends OAuth2Resource
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
    public final function verbs()
    {
        $verbs = parent::verbs();
        unset($verbs['index'], $verbs['create'], $verbs['delete']);
        $verbs['cambio_password'] = [
            'patch'
        ];
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
            'cambio_password' => ['class' => CambiaPassword::class]
        ];
    }
}
