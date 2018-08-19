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
class SafeDeleteChild extends \app\models\SafeDeleteChild implements Linkable, Embeddable
{
    use EmbeddableTrait {
        EmbeddableTrait::toArray as embedArray;
    }
    /**
     * @inheritdoc
     */
    public function toArray(
        array $fields = [],
        array $expand = [],
        $recursive = true
    ) {
        return $this->embedArray(
            $fields ?: $this->attributes(),
            $expand,
            $recursive
        );
    }
    /**
     * @inheritdoc
     */
    protected $safeDeleteClass = safeDelete::class;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'slug' => [
                'class' => Slug::class,
                'resourceName' => 'child',
                'parentSlugRelation' => 'safeDelete',
            ],
            'curies' => Curies::class,
        ]);
    }
    /**
     * @inheritdoc
     */
    public function getLinks()
    {
        return array_merge($this->getSlugLinks(), $this->getCuriesLinks(), [
            'safe-delete' => $this->getSelfLink() . '/safe-delete',
        ]);
    }
}