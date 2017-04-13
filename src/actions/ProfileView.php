<?php

namespace tecnocen\roa\actions;

use Yii;

class ProfileView extends \yii\rest\Action
{
    /**
     * @inheritdoc
     */
    public function init()
    {
    }

    /**
     * Shows the information of the logged user.
     *
     * @return yii\web\IdentityInterface
     */
    public function run()
    {
        return Yii::$app->user->identity;
    }
}
