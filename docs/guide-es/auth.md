Autenticacion
=============

La autenticacion se realiza utilizando [[OAuth2]] con los filtros:

- `tecnocen\oauth2server\filters\auth\CompositeAuth`
- `yii\filters\auth\HttpBearerAuth`
- `yii\filters\auth\QueryParamAuth`

Anexar en Api Container

```php

use tecnocen\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class Api extends \tecnocen\roa\modules\ApiContainer
{
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'oauth2Module' => $this->getOauth2Module(),
                'authMethods' => [
                    ['class' => HttpBearerAuth::class],
                    [
                        'class' => QueryParamAuth::class,
                        // !Important, GET request parameter to get the token.
                        'tokenParam' => 'accessToken',
                    ],
                ],
                 // no requerir token para generar token
                'except' => [$this->oauth2ModuleId . '/*'],
            ],
        ];
    }
}
```

Esto hace que todas las versiones dadas de alta en el contenedor deban estar
autenticadas excepto las del submodulo `oauth2`,

Anexar en Api Version
---------------------

Se puede anexar a una instancia de version de api desde el contenedor:

```php

use tecnocen\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class Api extends \tecnocen\roa\modules\ApiContainer
{
    public $versions = [
        'v1' => [
            'class' => v1/Version::class,
            'as authenticator' => [
                'class' => CompositeAuth::class,
                'oauth2Module' => 'api/oauth2',
                'authMethods' => [
                    ['class' => HttpBearerAuth::class],
                    [
                        'class' => QueryParamAuth::class,
                        // !Important, GET request parameter to get the token.
                        'tokenParam' => 'accessToken',
                    ],
                ],
            ],
        ],
    ];
}
```

o en la declaracion de la clase.

```php
use tecnocen\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class V1 extends \tecnocen\roa\modules\ApiVersion
{
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'oauth2Module' => $this->owner->getOauth2Module(),
                'authMethods' => [
                    ['class' => HttpBearerAuth::class],
                    [
                        'class' => QueryParamAuth::class,
                        // !Important, GET request parameter to get the token.
                        'tokenParam' => 'accessToken',
                    ],
                ],
            ],
        ];
    }
}
```

Anexar a Recurso
----------------

Por ultimo se puede definir la autenticacion por recurso de forma individual.

Esto se puede hacer desde la version a la que pertenece.


```php

use tecnocen\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class V1 extends \tecnocen\roa\modules\ApiVersion
{
    public $resources = [
        'shop' => [
            'class' => resources/ShopResource::class,
            'as authenticator' => [
                'class' => CompositeAuth::class,
                'oauth2Module' => 'api/oauth2',
                'authMethods' => [
                    ['class' => HttpBearerAuth::class],
                    [
                        'class' => QueryParamAuth::class,
                        // !Important, GET request parameter to get the token.
                        'tokenParam' => 'accessToken',
                    ],
                ],
            ],
        ],
    ];
}
```

o en la declaracion de la clase.

```php
use tecnocen\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class ShopResource extends \tecnocen\roa\controllers\Resource
{
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'oauth2Module' => $this->owner->owner->getOauth2Module(),
                'authMethods' => [
                    ['class' => HttpBearerAuth::class],
                    [
                        'class' => QueryParamAuth::class,
                        // !Important, GET request parameter to get the token.
                        'tokenParam' => 'accessToken',
                    ],
                ],
            ],
        ];
    }
}
```

Notese que si se define la autenticacion usando mas de un metodo descrito,
todos se ejecutaran a la vez por lo que es necesario usar `$except` y `$only`
para evitar conflictos.

[OAuth2]: https://oauth.net/2/
