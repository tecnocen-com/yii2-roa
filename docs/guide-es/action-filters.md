Filtros de Accion
=================

Las clases que extienden [ActionFilter] definen comportamientos para
ejecutarse antes y despues de ejecutarse una accion.

ActionFilter en Yii2

La mayoria de los filtros tales como [Cors], [HostControl], [HttpCache],
[PageCache] y [RateLimiter] no tienen implementacion por defecto y se pueden
usar tal y como se detalla en la [Guia de Yii2].

Funcionalidades Implementadas en ROA
------------------------------------

Existen funcionalidades de terceros o que ya tienen un soporte por defecto en
ROA que es necesario resaltar.

### Autenticacion

La autenticacion [OAuth2] es soportada con los filtros:

- `tecnocen\oauth2server\filters\auth\CompositeAuth`
- `yii\filters\auth\HttpBearerAuth`
- `yii\filters\auth\QueryParamAuth`

```php
public function behaviors()
{
    return [
        'authenticator' => [
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
    ];
}
```

> Por defecto esta definido en `tecnocen\roa\modules\ApiContainer::behaviors()`.
> al extender este metodo hay que tener esto en cuenta.

### Negociacion de Contenido

Negociacion de Contenido es soportado con el filtro
`yii\filters\ContentNegotiator`.

```php
public function behaviors()
{
    return [
        'contentNegotiator' => [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => 'json',
                'application/xml' => 'xml',
            ],
            'languages' => [
                'en',
                'de',
            ],
        ],
    ];
}
```

> Por defecto esta definido en `tecnocen\roa\modules\ApiContainer::behaviors()`.
> al extender este metodo hay que tener esto en cuenta.

### Control de Acceso

El control de acceso es mas complejo ya que no solo se soporta con el filtro
`yii\filters\AccessControl` si no ademas con metodos
`tecnocen\roa\controllers\Resource::checkAccess()`,
`tecnocen\roa\behaviors\Slug::checkAccess()` y paradigmas como [RBAC].

[Articulo de Control de Acceso en ROA]

Metodos de Uso
--------------

Los filtros de accion se pueden usar de varias maneras tanto en los recursos
como en el modulo de contenedor o en los modulos de versiones dependiendo del
alcance que se necesite para cada funcionalidad.

### Anexar en Api Container

```php
use tecnocen\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

class Api extends \tecnocen\roa\modules\ApiContainer
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
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
        ]);
    }
}
```

Esto hace que todas las versiones dadas de alta en el contenedor.

> Por defecto el metodo contiene soporte para autenticacion y content negotiator
> por lo que se deben redefinir estas funcionalidades al sobre escribir el
> metodo o invocar `parent::behaviors()` como en el ejemplo

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
para evitar colisiones.

[ActionFilter]: https://www.yiiframework.com/doc/api/2.0/yii-base-actionfilter
[Cors]: https://www.yiiframework.com/doc/api/2.0/yii-base-cors
[HostControl]: https://www.yiiframework.com/doc/api/2.0/yii-base-hostcontrol
[HttpCache]: https://www.yiiframework.com/doc/api/2.0/yii-base-httpcache
[PageCache]: https://www.yiiframework.com/doc/api/2.0/yii-base-pagecache
[RateLimmiter]: https://www.yiiframework.com/doc/api/2.0/yii-base-actionfilter
[Guia de Yii2]: https://www.yiiframework.com/doc/guide/2.0/en/structure-filters
[OAuth2]: https://oauth.net/2/
[Articulo de Control de Acceso en ROA]: access-control.md
