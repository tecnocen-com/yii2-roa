Recursos de Archivos
====================

Algunos recursos necesitan subir y descargar archivos, además estos archivos
deben ser accesibles mediante recursos ROA para por ejemplo validar que se
tengan los permisos de acceso y que estos sigan el ciclo de vida de recursos.

Para ello se emplean varias clases que permitan su manejo como recursos ROA.

tecnocen\roa\FileRecord
-----------------------

Interfaz de PHP la cual facilita usar el método `yii\web\Response::sendFile()`
para devolver un archivo como respuesta de una petición.

```php
namespace backend\api\v1\models;

use tecnocen\roa\FileRecord;
use yii\web\Linkable;

/**
 * @property string $name
 * @property string $picture file name to the picture of the product.
 * @method string getSelfLink()
 */
class Product extends \common\models\Product implements FileRecord, Linkable
{
    public function filePath($ext)
    {
        // check the file with the extension $ext exists, if not throw
        // NotFoundHttpException

        // Return the full file path
        return \Yii::getAlias("@web/uploads/products/{$this->picture}.$ext");
    }

    public function fileName($ext)
    {
        // Default name when the end user wants to download a file.
        // can return `null` to use the route name.

        return "{$this->name}.$ext";
    }

    public function mimeType($ext)
    {
        // the MIME Type of the content, can be null to default
        // application/octet-stream

        return \yii\helpers\FileHelper::getMimeTypeByExtension(".$ext");
    }

    public function links()
    {
        return [
            // return all needed links such as `self` and add a link to download
            // the available file streams.
            'self' => $this->getSelfLink(),
            'jpg-file' => $this->getSelfLink() . '.jpg',
            'png-file' => $this->getSelfLink() . '.png',
        ];
    }
}
```

> Nota: Toda la lógica para almacenar el archivo y guardar el registro
  debe implementarse de forma independiente.

tecnocen\roa\controllers\Resource
---------------------------------

En la clase del Recurso, se debe configurar qué archivos cargar al modelo.


```php

use backend\api\v1\Product;

class ProductResource extends \tecnocen\roa\controllers\Resource
{
    public $modelClass = Product::class;

    public $createFileAttributes = ['picture'];

    public $updateFileAttributes = ['picture'];
     
}
```

### tecnocen\roa\actions\FileStream

La clase Resource reconocerá automaticamente `Product` como un registro de
archivo y configurará esta acción por defecto, opcionalmente esto se puede
modificar en el método `actions()`.

```php
class ProductResource extends \tecnocen\roa\controllers\Resource
{
    public function actions()
    {
        $actions = parent::actions();
        $actions['file-stream']['checkAccess'] = function () {
            // something
        };

        return $actions;
    }
}
```

tecnocen\roa\urlRules\File
--------------------------

Finally all thats left is to tell the url manager that the resource can return
files via the api version module.

This rule also enables [POST] requests on update to use `multipart/form-data`

```php

use tecnocen\roa\urlRules\File as FileUrlRule;

class Version extends \tecnocen\roa\modules\ApiVersion
{
    public $resources = [
        'product' => [
            'urlRule' => [
                'class' => FileUrlRule::class,
                // optional, which extensions are allowed, this are default
                'ext' => ['png', 'jpg']
            ],
        ],
        // other resoures.
   ];
}
```
