Crear Pruebas para Recursos ROA
===============================

Los recursos en ROA son bastante predecibles y su comportamiento esta bien
definido. Por lo tanto el proceso de crear pruebas resulta repetitivo y tedioso.

Pero eso también implica que el proceso de crear pruebas automaticas puede ser
automatizado.

Interfaz `Tester`
-----------------

La interfaz `tecnocen\roa\test\Tester` define los metodos que se emplearan para
probar un recurso ROA desde el punto de vista de un Actor de Codeception.

Para implementarla se puede usar el trato `tecnocen\roa\test\Tester`.

```php
use tecnocen\roa\test\Tester as RoaTester;
use tecnocen\roa\test\TesterTrait as RoaTesterTrait;

class ApiTester extends \Codeception\Actor implements RoaTester
{
    use _generated\ApiTesterActions;
    use RoaTesterTrait;
}
```

La aplicación Yii2 App ROA ya tiene el soporte por defecto.

Clase `AbstractResourceCest`
----------------------------

La clase `tecnocen\roa\test\AbstractResourceCest` provee soporte para realizar
pruebas de recursos rest tanto en aplicaciones como en librerias.

### Uso

Para utilizar esta clase se crea una clase que la extienda del tipo CEST.

Una vez creada es necesario definir los metodos protegidos `recordJsonType()`
y `getRoutePattern()`.

```php
class ShopCest extends \tecnocen\roa\test\AbstractResourceCest
{
    
    /**
     * @inheritdoc
     */
    protected function recordJsonType()
    {
        return [
            'id' => 'integer:>0',
            'name' => 'string',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getRoutePattern()
    {
        return 'v1/shop';
    }
}
```

### Metodos `internal`

La clase `AbstractResourceCest` define metodos que ejecutan las pruebas
más comunes de un recurso ROA.

- `internalIndex`
- `internalView`
- `internalCreate`
- `internalUpdate`
- `internalDelete`

Todos ellos reciben los mismos parametros `Tester $I, Example $example`,
la diferencia esta en los datos que `$example` necesita obtener. En el
caseo de `internalIndex` puede usarse de la siguiente forma:

```php
    /**
     * @param  ApiTester $I
     * @param  Example $example
     * @dataprovider indexDataProvider
     */
    public function index(ApiTester $I, Example $example)
    {
        $I->wantTo('Retrieve list of Shop records.');
        $this->internalIndex($I, $example);
    }
   
    /**
     * @return array<string,array> for test `index()`.
     */
    protected function indexDataProvider()
    {
        return [
            'list' => [
                'httpCode' => HttpCode::OK,
            ],
            'filter by name' => [
                'urlParams' => [
                    'name' => 'Miniso',
                    'expand' => 'employees'
                ],
                'httpCode' => HttpCode::OK,
                'headers' => [
                    'X-Pagination-Total-Count' => 1,
                ],
            ],
        ];
    }
```

La documentación phpdoc de cada metodo detalla al respecto.

Todos los metodos `internal` son protegidos por lo que no
se ejecutan como pruebas si no que deben ser invocados
desde un metodo público.
