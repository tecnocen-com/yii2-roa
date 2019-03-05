Slug Behavior
=============

Algunos recursos necesitan estar configurados de forma "aninada" con el fin de que la
ruta representacional incluya la información de los padres para su consumo.

`tienda/1/almacen/3/seccion/5`

Con el requerimiento que si no se tienen los permisos para acceder a `tienda/1`
tampoco se podrá acceder a ninguno de los servicios anidados.

La clase `tecnocen\roa\behaviors\Slug` proporciona funcionalidad para registros
cuyos recursos se aniden con otros recursos de la misma versión.

Uso
---

```php
use tecnocen\roa\behaviors\Slug;

public function behaviors()
{
    return [
        [
            'class' => Slug::class,
            'resourceName' => 'seccion', // se usará para crear los enlaces
            'parentSlugRelation' => 'almacen', // relación de anidado.
            'checkAccess' => function ($params) {
                $user = Yii::$app->getUser();
                if (!$user->can('manager')
                    && !$this->responsable_id == $user->id
                ) {
                    throw new \yii\web\ForbiddenHttpException(
                        'No tiene permisos para editar este registro.'
                    );
                }

                if (isset($params['almacen_id'])
                    && $this->id != $params['almacen_id']
                ) {
                    throw new \yii\web\NotFoundHttpException(
                        "Registro no asociado al almacen {$params['almacen_id']}."
                    );
                }
            }
        ]
    ];
}


public function getAlmacen()
{
    return $this->hasOne(Almacen::class, ['id' => 'almacen_id']);
}
```

Método checkAccess()
--------------------

El método `tecnocen\roa\behaviors\Slug::checkAccess()` sirve para que cada
registro compruebe si esta disponible para su acceso. Este método se manda
invocar retroactivamente en los recursos que tengan declarada al registro
como relación `parentSlugRelation`.

`tienda/1/almacen/3/seccion/5`

Al invocar `checkAccess()` se invoca para los registros tienda con id 1,
almacen con id 3 y sección con id 5.

El método recibe como parámetro un `array` con las los parámetros `GET`
recibidos en  la petición. Y debe arrojar excepciones `yii\web\HttpException`
cuando no se permita el acceso.

La firma completa de la firma es

```php
function checkAccess(string[] $params)
    throws \yii\web\HttpException
{
}
```

Links
-----

Slug behavior tiene métodos `getSelfLink()` y `getSlugLinks()` los cuales
automatizan la creación de enlaces relacionales mandando a llamar recursivamente
los enlaces relacionades de las relaciones definidas en `parentSlugRelation`
