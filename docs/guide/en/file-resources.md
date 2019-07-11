File Resources
==============

Some resources need upload and download files, also this files must be
accesible through ROA resources, for example to validate that access
permits are available and that they follow the resources life cycle.

For this, several classes are used that allow their management as ROA
resources.

tecnocen\roa\FileRecord
-----------------------

PHP Interface that facilitates the use of method `yii\web\Response::sendFile()`
to return a file as a response of request.

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

> Note: all the logic to actually store the file and save the record must be
  implemented independently.

tecnocen\roa\controllers\Resource
---------------------------------

On the resource class you must configure which files to load to the model.


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

The Resource class will automatically recognize `Product` as a file record and
configure this action by default, you can optionally modify it on the `actions()`
method.

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
