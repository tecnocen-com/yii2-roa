<?php

namespace tecnocen\roa\actions;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Trait to configure and load uploaded files to a model.
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
trait LoadFileTrait
{
    /**
     * @var string[] that defines which attributes will be recibe files.
     *
     * Example
     * ```php
     * [
     *     // 'avatar' attribute load file uploaded as 'avatar'
     *     'avatar',
     *     // 'background_fullsize' attribute load file uploaded as 'background'
     *     'background_fullsize' => 'background',
     * ]
     * ```
     */
    public $fileAttributes = [];

    /**
     * Parse the allowed uploaded files.
     *
     * @return UploadedFile[] files sent to the action.
     */
    protected function parseFileAttributes()
    {
        $files = [];
        foreach ($this->fileAttributes as $attribute => $value) {
            if (is_int($attribute)) {
                $attribute = $value;
            }
            if (null !== ($uploadedFile = UploadedFile::getInstanceByName(
                $value
            ))) {
                $files[$attribute] = $uploadedFile;
            }
        }

        return $files;
    }
}
