Api Versión
===========

Las versiones de api declaradas en el contenedor de versiones son módulos que
extienden la clase `tecnocen\roa\modules\ApiVersion`.

Cada versión define su lista de recursos y el ciclo de vida de su publicación.

Demo de Clase de Versión
------------------------

```php
V1Version extends \tecnocen\roa\modules\ApiVersion
{
    public $releaseDate = '2010-06-15';
    public $deprecationDate = '2012-01-01';
    public $obsoleteDate = '2012-12-31';

    public $resources = [
        // toma la clase `backend\api\v1\resources\UserResource`
        'user',
        // toma la clase `backend\api\v1\resources\CommentResource`
        'comment',
        // toma la clase `backend\api\v1\resources\comment\ReplyResource`
        'comment/<comment_id:[\d]+>/reply',
        // toma la clase `backend\api\v1\resources\user\AvatarResource`
        'user/avatar',
        'forum' => resources\ForumResource::class, // ruta de la clase
        'forum/<forum_id:\d>/post' => [
            'class' => resources\PostResource,
            'urlRule' => [
                // configuracion especial ver guia de enrutamiento.
            ],
        ],
    ];

    public $apidoc = 'http://mockapi.com/api/v1';

    public $controllerNamespace = 'backend\api\v1\resources';

    public $controllerSubfix = 'Resource';
}
```

El uso de la llave `urlRule` en el ejemplo del recurso `post` se detalla en la
guia de [Enrutamiento](routing.md)

Recursos
--------

Los recursos de cada versión se declaran en la propiedad pública `$resources`
que consiste en un arreglo donde cada elemento declara un recurso.

Cada elemento de `$resources` puede consistir de una cadena para declarar la
ruta y automáticamente se deduce el controlador asociado o de una pareja
`'ruta' => 'recurso'` donde `'recurso'` es una especificacion como se define en
la [guia de recurso](roa-resource.md).

Api Documentation
-----------------

La propiedad `$apidoc` puede almacenar una URL para la documentacion del api.

Ciclo de Vida
-------------

Cada versión tiene un ciclo de vida el cual se compone de 4 etapas:
`Desarrollo`, `Estable`, `Depreciado` y `Obsoleto`.

El cambio de etapas se determina mediante las variables `$releaseDate`,
`$deprecationDate` y `$obsoleteDate`.

La estabilidad de cada versión se puede obtener con la variable de sólo lectura
`$stability`.

Descripción de las Etapas en el Ciclo de Vida
---------------------------------------------

### Desarrollo

Los recursos e interfaces no se consideran publicados por lo que pueden ser
alterados, también se pueden agregar nuevos recursos.

#### Políticas

- NO DEBE haber más de un mismo tipo de api en desarrollo.

### Estable

Al publicarse una versión se convierte en estable, esto significa que ya no se
desarrollan nuevas funcionalidades y se da mantenimiento activo conforme la
retroalimentación del usuario final.

Todo mantenimiento debe ser retrocompatible con los recursos e interfaces
publicados a partir de la fecha de liberación `$releaseDate`.

#### Políticas

- NO DEBERÍA haber más de un mismo tipo de api estable.
- NO DEBERÍAN de publicarse nuevos recursos.
- NO DEBEN eliminarse recursos publicados.
- NO DEBE de eliminarse funcionalidad de un recurso publicado.
- Las interfaces NO DEBEN eliminar atributos de la estructura de información.
- Se corrigen activamente fallos de ejecución y seguridad.


### Depreciado

Ya no se soporta activamente, correcciones de ejecución se ignoran y sólo se da
soporte a fallos de seguridad para el cliente final y el servidor.

#### Politicas

- NO DEBERÍA haber más de 2 apis depreciadas del mismo tipo.
- NO DEBEN publicarse nuevos recursos o funcionalidades.
- NO DEBERÍAN eliminarse recursos publicados.
- Los recursos eliminando DEBEN devolver un código de estado HTTP 410 GONE.
- NO DEBERÍA eliminarse la funcionalidad de un recurso publicado.
- Las interfaces NO DEBEN eliminar atributos de la estructura de información.
- Sólo se corrigen fallos de seguridad para el usuario final o el servidor.
- NO DEBEN corregirse errores de ejecución.
- DEBERÍA recibir mantenimiento de seguridad por al menos 6 meses.
- NO DEBERÍA recibir mantenimiento de seguridad por más de 12 meses.

### Obsoleto

Al llegar al final del ciclo de vida, la api y los recursos ya no están
disponibles para su consumo.

#### Políticas

- Todos los recursos DEBEN devolver un código de estado HTTP 410 GONE

Transiciones de Estabilidad
---------------------------

### Publicación

Se le llama 'publicación' al proceso de cambiar la estabilidad de una versión
de desarrollo a estable.

Una vez que se han completado los recursos para consumir los flujos de negocio
de la aplicación y estos han sido verificados como funcionales, se puede cambiar
la estabilidad de un módulo de versión.

Para publicar un módulo de versión basta con definir el atributo `$releaseDate`
con la fecha a partir de la cual el módulo se considera estable.

```php
    'releaseDate' => '2020-06-15',
```

### Depreciación

Se le llama 'depreciación' al proceso de cambiar la estabilidad de una versión de
estable a depreciada.

Generalmente se asocia el publicar una versión nueva con depreciar la versión
anteriormente soportada.

Para depreciar un módulo de versión es necesario definir los atributos
`$deprecationDate` y `$endOfLifeDate`.

```php
    'releaseDate' => '2020-06-15',
    'deprecationDate' => '2021-06-01',
    'obsoleteDate' => '2021-12-31',
	```

### Fin de Ciclo de Vida

Se le llama 'fin de ciclo de vida' al proceso de cambiar la estabilidad de una
versión de depreciada a no soportada.

Este proceso es automático en cuanto el contenedor detecta una versión con una
fecha `$obsoleteDate` menor a la fecha actual.

Recurso de informacion de version
---------------------------------

Al acceder a la ruta base de la version se puede encontrar la documentacion de
sus ciclo de vida, coleccion de rutas y el apidoc.
