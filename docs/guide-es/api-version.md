Api Version
===========

Las versiones de api declaradas en el contenedor de versiones son modulos que
extienden la clase `tecnocen\roa\modules\ApiVersion`.

Cada versión define su lista de recursos y el ciclo de vida de su publicación.

Demo de Clase de Version
------------------------

```php
V1Version extends \tecnocen\roa\modules\ApiVersion
{
    public $releaseDate = '2010-06-15';
    public $deprecationDate = '2012-01-01';
    public $obsoleteDate = '2012-12-31';

    public $resources = [
        'user', // UserController
        'comment', // CommentController
        'comment/<comment_id:[\d]+>/reply', // CommenReplyController
        'user/avatar', // UserAvatarController
        'user/avatar.<ext:[jpg|png]>' => UserAvatarFileController::class,
    ];
}
```

Recursos
--------

Los recursos de cada versión se declaran en la propiedad pública `$resources`
que consiste en un arreglo donde cada elemento declara un recurso.

Cada elemento de `$resources` puede con sistir de una cadena para declarar la
ruta y automaticamente se deduce el controlador asociado o de una pareja
`'ruta' => 'clase'` para especificar una controlador especifico.

Ciclo de Vida
-------------

Cada versión tiene un ciclo de vida el cual se compone de 4 etapas:
`Desarrollo`, `Estable`, `Deprecado` y `Obsoleto`.

El cambio de etapas se determina mediante las variables `$releaseDate`,
`$deprecationDate` y `$obsoleteDate`,

La estabilidad de cada versión se puede obtener con la variable de sólo lectura
`$stability`.

Descripción de las Etapas en el Ciclo de Vida
---------------------------------------------

### Desarrollo

Los recursos e interfaces no se consideran publicados por lo que pueden ser
alterados, también se pueden agregar nuevos recursos 

#### Politicas

- NO DEBE haber más de un mismo tipo de api en desarrollo.

### Estable

Al publicarse una versión se convierte es estable, esto significa que ya no se
desarrollan nuevas funcionalidades y se da mantenimiento activo conforme la
retroalimentación del usuario final.

Todo mantenimiento debe ser retroincompatible con los recursos e interfaces
publicadas a partir de la fecha de liberación.

#### Politicas

- NO DEBERÍA haber más de un mismo tipo de api estable.
- NO DEBERÍAN de publicarse nuevos recursos.
- NO DEBEN eliminarse recursos publicados.
- NO DEBE de eliminarse funcionalidad de una recurso publicado.
- Las interfaces NO DEBEN eliminar atributos de la estructura de información.
- Se corrigen activamente fallos de ejecución y seguridad.


### Deprecado

Ya no se soporta activamente, correcciones de ejecución se ignoran y sólo se da
soporte a fallos de seguridad para el cliente final y el servidor.

#### Politicas

- NO DEBERÍA haber más de 2 apis deprecadas del mismo tipo
- NO DEBEN publicarse nuevos recursos o funcionalidades
- NO DEBERÍAN eliminarse recursos publicados.
- Los recursos eliminando DEBEN devolver un código de estado HTTP 410 GONE
- NO DEBERÍA eliminarse la funcionalidad de un recurso publicado
- Las interfaces NO DEBEN eliminar atributos de la estructura de información.
- Sólo se corrigen fallos de seguridad para el usuario final o el servidor.
- NO DEBEN corregirse errores de ejecución
. DEBERÍA recibir mantenimiento de seguridad por al menos 6 meses.
- NO DEBERÍA recibir mantenimiento de seguridad por más de 12 meses.

### Obsoleto

Al llegar al final del ciclo de vida, la api y los recursos ya no están
disponibles para su consumo.

#### Politicas

- Todos los recursos DEBEN devolver un código de estado HTTP 410 GONE

Transiciones de Estabilidad
---------------------------

### Publicación

Se le llama 'publicación' al proceso de cambiar la estabilidad de una versión
de desarrollo a estable.

Una vez que se han completado los recursos para consumir los flujos de negocio
de la aplicación y estos han sido verificados como funcionales, se puede cambiar
la estabilidad de un modulo de versión.

Para publicar un modulo de versión basta con definir el atributo `$releaseDate`
con la fecha a partir de la cual el modulo se considera estable.

```php
    'releaseDate' => '2020-06-15',
```

### Deprecación

Se le llama 'deprecación' al proceso de cambiar la estabilidad de una versión de
estable a deprecada.

Generalmente se asocia el publicar una versión nueva con deprecar la versión
anteriormente soportada.

Para deprecar un modulo de versión es necesario definir los atributos
`$deprecationDate` y `$endOfLifeDate`.

```php
    'releaseDate' => '2020-06-15',
    'deprecationDate' => '2021-06-01',
    'obsoleteDate' => '2021-12-31',
```

### Fin de Ciclo de Vida

Se le llama 'fin de ciclo de vida' al proceso de cambiar la estabilidad de una
versión de deprecada a no soportada.

Este proceso es automatico en cuanto el contenedor detecta una versión con una
fecha `$obsoleteDate` menor a la fecha actual.
