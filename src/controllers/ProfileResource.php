<?php

namespace tecnocen\roa\controllers;

use tecnocen\roa\actions\ProfileUpdate;
use tecnocen\roa\actions\ProfileView;
use yii\filters\VerbFilter;
use yii\rest\OptionsAction;

class ProfileResource extends \yii\rest\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            // content negotiator, autenticator, etc moved by default to
            // api container
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
        ];
    }

    /**
     * @inheridoc
     */
    protected function verbs()
    {
        $verbs = ['GET', 'PUT', 'PATCH', 'OPTIONS'];

        return [
            'view' => $verbs,
            'update' => $verbs,
            'options' => $verbs,
        ];
    }

    /**
     * @inheridoc
     */
    public function actions()
    {
        return [
            'view' => ['class' => ProfileView::class],
            'update' => ['class' => ProfileUpdate::class],
            'options' => ['class' => OptionsAction::class],
        ];
    }
}
