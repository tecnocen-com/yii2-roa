Contenedor de API
-----------------

El contenedor de versiones de api se configura como módulo de la aplicación.

Dentro del contenedor se definen las versiones soportadas por el api.

El módulo de contendedor de versiones debe extender la clase
`tecnocen\roa\modules\ApiContainer`.

Ejemplo de Módulo Contenedor de Api
-----------------------------------

> backend/config/main.php
```php
    'bootstrap' => ['api'],
    'modules' => [
        'api' => ['class' => BackendApi::class],
    ],
```

```php
class BackendApi extends \tecnocen\roa\modules\ApiContainer
{

   public $identityClass => models\User::class;

   public $versions = [
       'v1' => ['class' => v1\Version::class],
       'v2' => ['class' => v2\Version::class],
   ];
}
```

Propiedad `$versions`
---------------------

La propiedad `$versions` declarará las versiones soportadas y las clases de
los módulos asociados a cada versión.

El índice determina el identificador de las versiones así como la ruta de
consumo de cada versión.

Propiedad `$identiyClass`
-------------------------

Esta propiedad declara a la clase usada para identificar al usuario que consume
el api.

Permite al módulo de api rescribir la propiedad `Yii::$app->identityClass` con
este valor antes de crear los recursos del api.

Esto permite cambiar la forma de acceso del usuario para usar tokens en lugar de
sesiones y cookies.

Detalle de las Versiones
------------------------

Se puede realizar una solicitud a la ruta de la versión para encontrar el detalle
de las versiones incluyendo su estabilidad.

![Detalle de Versiones](../versions-detail.png)

[Documentación de Módulos de Version](api-version.md)
