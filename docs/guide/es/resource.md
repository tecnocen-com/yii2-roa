Clase de Recurso ROA
====================

La clase `tecnocen\roa\controllers\Resource` es la clase que se ofrece
para crear controladores/recursos de ROA.

Demo de recurso
---------------

El siguiente demo ejemplifica como dar de alta un recurso ROA.

### Modelo

El contrato de los datos recibidos y devueltos por un recurso ROA es manejado
mediante modelos de la misma forma que en yii2.

Se recomienda crear modelos especializados que sirvan como contratos que
extiendan la funcionalidad basica para obtener soporte de HAL.

```php
use tecnocen\roa\behaviors\Curies;
use tecnocen\roa\behaviors\Slug;
use tecnocen\roa\hal\Embeddable;
use tecnocen\roa\hal\EmbeddableTrait;
use yii\web\Linkable;

class Shop extends \common\models\Shop implements Embeddable, Linkable
{
    use \tecnocen\roa\hal\EmbeddableTrait;

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'slug' => [
                'class' => Slug::class,
                'resourceName' => 'shop',
            ],
            'curies' => Curies::class,
        ]);
    }

    public function getLinks()
    {
        return array_merge($this->getSlugLinks(), $this->getCuriesLinks(), [
            'employees' => $this->getSelfLink() . '/employee',
        ]);
    }

    public function extraFields()
    {
        return ['employees'];
    }
}
``` 

Ver [slug-behavior.md],

### ApiVersion::$resources

Se debe declarar el nuevo recurso en este arreglo donde la llave es la ruta
biunivoca de la clase.

```php
class V1 extends \tecnocen\roa\modules\ApiVersion
{
    public $resources = [
        'shop' => [
            'class' => resources\ShopResource::class,
        ],
    ];
}
```

Automaticamente esto tambien dara de alta las reglas para el manejo de URL.
Ver [routing.md].

### Clase de Recurso.

```php
use backend\api\v1\models\Store;
use backend\api\v1\models\StoreSearch;

class StoreResource extends \tecnocen\roa\controllers\Resource
{
    public $modelClass = Store::class;

    public $searchClass = StoreSearch::class;

    public $updateScenario = 'api-update';

    public $createScenario = 'api-create';

    public $notFoundMessage = 'The store {id} doesn\'t exists.';
}
```


#### Propiedad `$modelClass`

Es la propiedad más importante del recurso, define el modelo que se usará para
las operaciones CRUD del recurso así cómo el contrato de la información
devuelta por el recurso mediante los atributos del modelo.

Se le pueden configurar escenarios para la creación y actualización de registros
mediante las propiedades `$updateScenario` y `$createScenario`.

Este modelo debe devolver su lista de relaciones soportadas mediante la
propiedad `_links`.

#### Propiedad `$searchClass`

Propiedad que define el modelo que crea el dataprovider para las búsquedas y
paginados del sitio así como el contrato de los atributos que se pueden enviar
a una búsqueda.

Si esta propiedad es nula entonces el recurso creará un dataprovider mediante
los metodos `baseQuery()` e `indexQuery()`.

El modelo debe implementar un metodo `search()` que devuelva una instancia de
`yii\data\DataProviderInterface`.

#### Método `verbs()`

Define los verbos y por lo tanto las acciones soportadas por el recurso.

Se pueden desactivar acciones simplemente invocando `unset()`.

#### Método `checkAcess()`

Permite extender la funcionalidad de RBAC para evaluar los permisos del registro
accedido por el recurso.
