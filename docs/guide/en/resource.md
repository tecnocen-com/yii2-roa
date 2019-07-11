ROA Resource Class
====================

Class `tecnocen\roa\controllers\Resource` is the class that is offered to create ROA 
controllers/resources.

Resource Demo
---------------

The following demo exemplifies how to register a ROA resource.

### Model

The contract of the data received and returned by a resource ROA is managed
using models in the same way as in yii2.

It is recommended to create specialized models that serve as contracts that
extend the basic functionality to obtain HAL support.

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

See [slug-behavior.md],

### ApiVersion::$resources

You must declare the new resource in this array where the key is the path
bijective of the class.

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

Automatically this will also give the rules for URL management.
See [routing.md].

### Resource Class.

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

#### Property `$modelClass`

It is the most important property of the resource, it defines the model that will be used to
the CRUD operations of the resource as well as the contract of the information
returned by the resource using the attributes of the model.

You can configure scenarios for creating and updating records
through the properties `$updateScenario` and `$createScenario`.

This model must return its list of supported relationships through the
property `_links`.

#### Property `$searchClass`

Property that defines the model that creates the dataprovider for searches and
pages of the site as well as the contract of the attributes that can be sent
to a search.

If this property is null then the resource will create a dataprovider through
methods `baseQuery()` and `indexQuery()`.

The model must implement a method `search()` to return an instance of
`yii\data\DataProviderInterface`.

#### Method `verbs()`

Define the verbs and therefore the actions supported by the resource.

You can deactivate actions simply by invoking `unset()`.

#### Method `checkAcess()`

Allows to extend RBAC functionality to evaluate registry permissions
accessed by the resource.
