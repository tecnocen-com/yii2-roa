<?php

namespace app\api\models;

use tecnocen\roa\behaviors\Curies;
use tecnocen\roa\behaviors\Slug;
use tecnocen\roa\hal\Embeddable;
use tecnocen\roa\hal\EmbeddableTrait;
use yii\web\Linkable;
/**
 * ROA contract to handle shop employee records.
 *
 * @method string[] getSlugLinks()
 * @method string getSelfLink()
 */
class File extends \app\models\File implements Linkable, Embeddable
{
    use EmbeddableTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'slug' => [
                'class' => Slug::class,
                'resourceName' => 'file',
            ],
            'curies' => Curies::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getLinks()
    {
        return array_merge($this->getSlugLinks(), $this->getCuriesLinks(), [
            'file-stream' => $this->getSelfLink() . substr($this->path, -4),
        ]);
    }
}
