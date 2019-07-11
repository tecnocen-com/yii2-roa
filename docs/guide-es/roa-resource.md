Clase de Recurso ROA
====================

La clase `tecncen\roa\controllers\Resource` implementa el comportamiento general
de un recurso en ROA.

Para poder utilizar la clase se requiere primero dar de alta el recurso en
`tecnocen\roa\modules\ApiVersion::$resources`, esta propiedad se detalla en la
guia de [Api Version](api-version.md).

Supongamos que se declara el siguiente recurso.


```php
    public $resources = [
        'store',
    ];
```

Luego se crea la clase del recurso

```php
use backend\api\v1\models\Store;
use backend\api\v1\models\StoreSearch;

class StoreResource extends \tecnocen\roa\controllers\Resource
{
    public $modelClass = Store::class;
}
```

El modelo `Store` debe de implementar `tecnocen\roa\hal\Embeddable`.

Propiedades
-----------

### Resource::$searchClass

El modelo `StoreSearch` debe implementar `tecnocen\roa\ResourceSearch` y en el
metodo `search()` devolver una instancia de `yii\data\DataProviderInterface` la
cual genere instancias de `Store`.

Si la propiedad `$searchClass` no se define entonces la busqueda se define por
defecto en base a las propiedades `$filterParams` y `$filterUser`.
