Clase de Recurso ROA
====================

La clase `tecnocen\roa\controllers\Oauth2Resource` es la clase que se ofrece
para crear controladores/recursos de ROA.

Demo de recurso
---------------

```php
use backend\api\v1\models\Store;
use backend\api\v1\models\StoreSearch;

class StoreResource extends \tecnocen\roa\controllers\Oauth2Resource
{
    public $modelClass = Store::class;

    public $searchClass = StoreSearch::class;

    public $updateScenario = 'api-update';

    public $createScenario = 'api-create';

    public $notFoundMessage = 'The store {id} doesn\'t exists.';

    protected function accessRules()
    {
        return [['allow' => true, 'roles' => ['manager', 'employee']]];
    }

    protected function cors()
    {
        return ArrayHelper::merge(parent::cors(), ['Origin' => ['store.com']]);
    }

    protected function allowedHosts()
    {
        return array_merge(parent::allowedHosts(), ['store.com']); 
    }
}
```

Propiedad `$modelClass`
-----------------------

Es la propiedad más importante del recurso, define el modelo que se usará para
las operaciones CRUD del recurso así cómo el contrato de la información
devuelta por el recurso mediante los atributos del modelo.

Se le pueden configurar escenarios para la creación y actualización de registros
mediante las propiedades `$updateScenario` y `$createScenario`.

Este modelo debe devolver su lista de relaciones soportadas mediante la
propiedad `_links`.

Propiedad `$searchClass`
------------------------

Propiedad que define el modelo que crea el dataprovider para las búsquedas y
paginados del sitio así como el contrato de los atributos que se pueden enviar
a una búsqueda.

Si esta propiedad es nula entonces el recurso creará un dataprovider mediante
los metodos `baseQuery()` e `indexQuery()`.

El modelo debe implementar un metodo `search()` que devuelva una instancia de
`yii\data\DataProviderInterface`.

Método `verbs()`
----------------

Define los verbos y por lo tanto las acciones soportadas por el recurso.

Se pueden desactivar acciones simplemente invocando `unset()`.

Método `checkAcess()`
---------------------

Permite extender la funcionalidad de RBAC para evaluar los permisos del registro
accedido por el recurso.

Método `cors()`
---------------

Define las cabeceras empleadas para poder implementar Cross Origin Resource
Sharing.
