<?php

namespace tecnocen\roa\actions;

use Yii;
use yii\rest\Serializer;
use yii\web\Response;
use backend\api\models\Usuario;

//ACTON PERSONALIZADO PARA CAMBIAR CONTRASEÑA
class CambiaPassword extends \yii\rest\Action
{

    public $scenario = 'cambia_password';
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
        //Se cargan los datos al modelo segun el usuario del token
        $model = Yii::$app->user->identity;
        $model->scenario = $this->scenario;
        $request = Yii::$app->request;

        //Valida la password actual introducida con la registrada
        if (!$model->validatePassword($request->post('password_actual'))) {
            $model->addError('password_actual', 'Contraseña actual incorrecta');
            return $model;
            
        }
        //Valida su ambas password coinciden, esto tambien aplica si una esta vacia
        if (!($request->post('password') === $request->post('passConfirm'))) {
            $model->addErrors([
                'passConfirm' => 'Las conraseñas no coinciden',
                'password' => 'Las conraseñas no coinciden',
            ]);
            return $model;
        }
        
        $model->password_actual = $request->post('password_actual');
        $model->password = $request->post('password');
        $model->passConfirm = $request->post('passConfirm');
        //Entra la validación de las rules
        if($model->validate([
                'password_actual',
                'password',
                'passConfirm'
            ])
        ) {
            $model->save();
        }

        return $model;

    }
}
