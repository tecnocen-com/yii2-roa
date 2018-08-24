<?php
namespace app\models;

use tecnocen\roa\FileRecord;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Model class for table `{{%shop_employee}}`
 *
 * @property integer $id
 * @property string $path
 * @property string $name
 * @property string $mime_type
 */
class File extends \yii\db\ActiveRecord implements FileRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['path'], 'required'],
            [['path'], 'file'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'path' => 'Path',
            'name' => 'Name',
            'mime_type' => 'Mime Type',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (!$this->path instanceof UploadedFile) {
            return true;
        }

        $newPath = Yii::$app->security->generateRandomString(8)
            . '.'
            . $this->path->extension;
        if (!$this->path->saveAs(
            Yii::getAlias("@app/uploads/$newPath"),
            true
        )) {
            $this->addError(
                'path',
                'Error code: '
                    . $this->path->error
                    . ' while saving the file.'
            );

            return false;
        }

        $this->name = $this->path->baseName;
        $this->mime_type = $this->path->type;
        $this->path = $newPath;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function filePath($ext)
    {
        if (!substr($this->path, -strlen($ext) - 1) === ".$ext") {
            throw new NotFoundHttpException('File not found.');
        }

        return Yii::getAlias("@app/uploads/{$this->path}");
    }

    /**
     * @inheritdoc
     */
    public function fileName($ext)
    {
        return $this->name . '.' . $ext;
    }

    /**
     * @inheritdoc
     */
    public function fileMimeType($ext)
    {
        return $this->mime_type;
    }
}
