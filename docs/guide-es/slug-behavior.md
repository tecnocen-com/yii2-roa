Slug Behavior
=============

Algunos recursos necesitan estar configurados de forma "aninada" de forma que la
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
                return $user->can('manager')
                    || $this->responsable_id == $user->id;
            }
        ]
    ];
}


public function getAlmacen()
{
    return $this->hasOne(Almacen::class, ['id' => 'almacen_id']);
}
```

Links
-----

Slug behavior tiene metodos `getSelfLink()` y `getSlugLinks()` los cuales
automatizan la creación de enlaces relacionales mandando a llamar recursivamente
los enlaces relacionades de las relaciones definidas en `parentSlugRelation`
