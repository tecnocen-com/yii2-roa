Enroutamiento
=============

yii2-roa define 3 classes que extienden de `yii\rest\UrlRule` para acceder a los
recursos de forma individual.

En la propiedad `tecnocen\roa\modules\ApiVersion::$resources` se puede agregar
una llave especial `urlRule` la cual configura individualmente la regla de
enroutamiento para Cada recurso.

```php
use tecnocen\roa\urlRules\File as FileUrlRule;

class V1 extends \tecnocen\roa\modules\ApiVersion {
    public $resources = [
        'documento' => [
            'class' => DocumentoResource::class,
            'urlRule' => [
                'class' => FileUrlRule::class, // clase de la regla
                'ext' => ['csv', 'xls', 'pdf', 'doc'], // extensiones soportadas
            ]
        ]
    ];
}
```

Esto permite envia runa petición `api/v1/documento/1.doc` para ejecutar acción
`file-stream` del recurso `DocumentoResource`.

Las clases provistas en este repositorio son

### tecnocen\roa\urlRules\Resource

Clase por defecto, permite enrutar los verbos de rest y parametros para
recursos con colecciones de datos, es decir que tienen listados de recursos al
hacer una petición [GET].

### tecnocen\roa\urlRules\File

Similar a la clase anterior sólo añade soporte para rutas con terminación
`{id}.{ext}` donde `{id}` es el identificador de un registro y `{ext}` es una
extensión valida configurada en la propiedad `$ext` de la clase.

### tecnocen\roa\urlRules\SingleRecord

Enroutamiento que sóolo soporta un registro y no colección, es decir acción
`index` no esta soportada por ejemplo para acceder a `perfil` que sólo es un
registro para cada usuario que acceda al sistema.


Optimizaciones
--------------

Se recomienda organizar las reglas de los recursos más usados al inicio para
evitar repetir procesos innecesarios.
