Clases de Pruebas
=================

Se proveén clases e interfaces de ayuda para facilitar escribir pruebas de
[Codeception Yii] enfocadas en [Pruebas Rest].

`tecnocen\roa\test\Tester`
--------------------------

Interfaz que solicita metodos especificos para ROA a `Codeception\Actor`. Se usa
al solicitarle a la clase `ApiTester` que la implemente.

También se proveé un trait `tecnocen\roa\test\TesterTrait` el cual implementa
todos los métodos necesarios.

```php

use Codeception\Actor
use tecnocen\roa\test\Tester as RoaTester;
use tecnocen\roa\test\TesterTrait as RoaTesterTrait;
use tecnocen\roa\test\AbstractResourceCest;
use tecnocen\roa\test\AbstractAccessTokenCest;

class ApiTester extends Actor implements RoaTester
{
    use RoaTesterTrait;
    // other traits and methods.
}
```

`tecnocen\roa\test\AbstractAccessTokenCest`
-------------------------------------------

Clase abstracta que proveé métodos protegidos que facilitan pruebas de creación
de tokens.

```php
class V1TokenCest extends \tecnocen\roa\test\AbstractAccessTokenCest
{
    /**
     * @dataprovider tokenData
     */
    public function oauth2Token(ApiTester $I, Example $I)
    {
        $I->wantTo('Generate OAuth2 Token');
        $I->generateToken($I, $example);
    }

    protected function tokenData()
    {
        return [
            [
                'client' => 'http_client',
                'clientPass' => 's3cr3t',
                'user' => 'aguevara',
                'userPass' => '244466666',
                'tokenName' => 'aguevara',
            ],
        ];
    }
}
```

`tecnocen\roa\test\AbstractResourceCest`
-------------------------------------------

Clase abstracta que proveé métodos protegidos que facilitan pruebas de recursos
ROA y sus respectivos verbos.

```php
class UserCest extends \tecnocen\roa\test\AbstractResourceCest
{
    /**
     * @dataprovider indexData
     */
    public function index(ApiTester $I, Example $I)
    {
        $I->wantTo('Generate Users List');
        $I->generateToken($I, $example);
    }

    protected function recordJsonType()
    {
        return [
            'id' => 'integer',
            'username' => 'string',
            'email' => 'string',
            '_links' => [
                'self' => ['href' => 'string:url'],
            ]
        ];
    }

    protected function getRoutePattern()
    {
        return 'users';
    }

    protected function indexData()
    {
        return [
            [
                'client' => 'http_client',
                'clientPass' => 's3cr3t',
                'user' => 'aguevara',
                'userPass' => '244466666',
                'tokenName' => 'aguevara',
            ],
        ];
    }
}
```

[Codeception Api]: http://codeception.com/for/yii
[Pruebas Rest]: http://codeception.com/docs/10-WebServices#REST
