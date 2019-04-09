Action Filters
=================

The classes that extend [ActionFilter] define behaviors for
run before and after an action is executed.

Yii2 ActionFilter 
--------------------

Most filters such as [Cors], [HostControl], [HttpCache],
[PageCache] and [RateLimiter] are not implemented by default and can be
use as detailed in the [Yii2 Guide].

Functionality Implemented in ROA
------------------------------------

There are functionalities of third parties or that already have a default support in
ROA that it is necessary to highlight.

### Authentication

Authentication [OAuth2] is supported with filters:

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

> By default this is defined in `tecnocen \ roa \ modules \ ApiContainer :: behaviors ()`.
> When extending this method, this must be taken into account.

### Content Negotiation

Content Negotiation is supported with the filter
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

> By default this is defined in `tecnocen \ roa \ modules \ ApiContainer :: behaviors ()`.
> When extending this method, this must be taken into account.

### Access control

Access control is more complex since it is not only supported with the filter
`yii\filters\AccessControl` if not also with methods
`tecnocen\roa\controllers\Resource::checkAccess()`,
`tecnocen\roa\behaviors\Slug::checkAccess()` and paradigms like [RBAC].

[Access Control Article in ROA]

Methods of Use
--------------

Action filters can be used in various ways in both resources
as in the container module or in the version modules depending on the
scope that is needed for each functionality.

### Append in Api Container

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

This causes all the versions registered in the container.

> By default the method contains support for authentication and content negotiator
> so these functions must be redefined when writing the
> method or invoke `parent :: behaviors ()` as in the example

Append in Api Version
---------------------

It can be appended to an api version instance from the container:

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

or in the class declaration.

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

Append to Resource
----------------

Finally you can define the authentication by resource individually.

This can be done from the version to which it belongs.

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

or in the class declaration.

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

> If an ActionFilter is used with more than one of the described methods,
> all will be executed at the same time so it is necessary to use `$ except` and` $ only`
> to avoid collisions.

[ActionFilter]: https://www.yiiframework.com/doc/api/2.0/yii-base-actionfilter
[Cors]: https://www.yiiframework.com/doc/api/2.0/yii-base-cors
[HostControl]: https://www.yiiframework.com/doc/api/2.0/yii-base-hostcontrol
[HttpCache]: https://www.yiiframework.com/doc/api/2.0/yii-base-httpcache
[PageCache]: https://www.yiiframework.com/doc/api/2.0/yii-base-pagecache
[RateLimmiter]: https://www.yiiframework.com/doc/api/2.0/yii-base-actionfilter
[Guia de Yii2]: https://www.yiiframework.com/doc/guide/2.0/en/structure-filters
[OAuth2]: https://oauth.net/2/
[RBAC]: https://www.yiiframework.com/doc/guide/2.0/en/security-authorization#rbac
[Articulo de Control de Acceso en ROA]: access-control.md
