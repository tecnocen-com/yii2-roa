API container
-----------------

The api version container is configured as the application module.

The versions supported by the API are defined within the container.

The version wrapper module must extend the class
`tecnocen\roa\modules\ApiContainer`.

Example of Api Container Module
-----------------------------------

> backend/config/main.php
```php
    'bootstrap' => ['api'],
    'modules' => [
        'api' => ['class' => BackendApi::class],
    ],
```

```php
class BackendApi extends \tecnocen\roa\modules\ApiContainer
{

   public $identityClass => models\User::class;

   public $versions = [
       'v1' => ['class' => v1\Version::class],
       'v2' => ['class' => v2\Version::class],
   ];
}
```

Property `$versions`
---------------------

The `$ versions` property will declare the supported versions and the classes of
the modules associated with each version.

The index determines the identifier of the versions as well as the route of
consumption of each version.

Property `$identiyClass`
-------------------------

This property declares the class used to identify the user who consumes
the api

Allow the api module to rewrite the property `Yii :: $ app-> identityClass` with
this value before creating the api resources.

This allows changing the user's access to use tokens instead of
sessions and cookies.

Detail of the Versions
------------------------

You can make a request to the version path to find the detail
of the versions including their stability.

![Detail of Versions](../versions-detail.png)

[Version Modules Documentation](api-version.md)
