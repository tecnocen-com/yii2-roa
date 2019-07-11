Control de Accesos
==================

El control de Accesos en Yii2 ROA es complicado para poder cubrir varios casos
de uso.

yii\filters\AccessControl
-------------------------

[AccessControl](https://www.yiiframework.com/doc/api/2.0/yii-filters-accesscontrol)
es un ActionFilter de Yii2 que permite declarar reglas de acceso el cual se puede
axexar en controladores o modulos.

Al ser un ActionFilter de Yii2 se puede usar como se describe en la guía de
[Filtros de Accion](action-filters.md)

Cubre el caso de uso de dar soporte a modulos completos o grupos de
controladores mediante la configuración de `$only` y `$except`.

CheckAccess
-----------

Hay varios métodos declarados como 'checkAccess' cubriendo diferentes casos de
uso y formas de ser declarados.

### tecnocen\roa\controllers\Resource::checkAccess()

Extiende de
[yii\rest\ActiveController](https://www.yiiframework.com/doc/api/2.0/yii-rest-activecontroller)
Cubre el caso de uso de validar el acceso para un controlador específico.

```php
class ShopResource extends \tecnocen\roa\Resource
{
    public function checkAccess($action, $model = null, $params = [])
    {
        // ...
    }
}
```

### tecnocen\roa\actions\Action::$checkAcccess

Extiende de [yii\rest\Action] cubre el caso de uso de validar una acción
específica.

La firma de la función anónima cambia agregando un argumento `$params` que
contiene los parámetros enviados mediante POST y GET.


```php
class ShopResource extends \tecnocen\roa\Resource
{
    public function actions()
    {
        $actions = parent::actions();

        $actions['create']['checkAccess'] = function (
            \tecnocen\roa\Action $action,
            \yii\db\ActiveRecordInterface $model = null,
            array $params = []
        ) {
            // ...
        }

        return $actions;
    }
}
```

### tecnocen\roa\behaviors\Slug::$checkAccess

La clase Slug se anexa a los modelos  para generar links anidados de registros
y además revisar que se tenga acceso a cada sección de la ruta roa.

Por ejemplo si se tiene, hace una petición a la ruta `shop/1/section/3/aisle/5`
se ejecuta la validación de `checkAccess` en `aisle` de id 5, section de id 3
y `shop` de id 1 en ese orden.

```php
class Shop extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            'slug' => [
                'class' => \tecnocen\roa\behaviors\Slug::class,
                'checkAccess' => function (array $params) {
                    // ...
                },
            ],
        ];
    }
}
```

Donde `$params` son los parámetros enviados mediante POST y GET.
