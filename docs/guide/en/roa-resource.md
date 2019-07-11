ROA Resource Class
====================

Class `tecncen\roa\controllers\Resource` implements the general behavior 
of a resource in ROA.

In order to use the class, it is first necessary to register the resource in
`tecnocen\roa\modules\ApiVersion::$resources`, this property is detailed in the
guide of [Api Version] (api-version.md).

Suppose that the following resource is declared.

```php
    public $resources = [
        'store',
    ];
```

Then the resource class is created

```php
use backend\api\v1\models\Store;
use backend\api\v1\models\StoreSearch;

class StoreResource extends \tecnocen\roa\controllers\Resource
{
    public $modelClass = Store::class;
}
```

The `Store` model must implement `tecnocen\roa\hal\Embeddable`

Property
-----------

### Resource::$searchClass

`StoreSearch` model must implement `tecnocen\roa\ResourceSearch` and in the
method `search ()` return an instance of `yii\data\DataProviderInterface` the
which generates instances of `Store`.

If the `$searchClass` property is not defined then the search is defined by
defect based on the properties `$ filterParams` and` $ filterUser`.
