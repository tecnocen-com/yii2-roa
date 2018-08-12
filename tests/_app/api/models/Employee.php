<?php

namespace app\api\models;

use src\behaviors\Curies;
use src\behaviors\Slug;
use src\hal\Embeddable;
use src\hal\EmbeddableTrait;
use yii\web\Linkable;
/**
 * ROA contract to handle shop employee records.
 *
 * @method string[] getSlugLinks()
 * @method string getSelfLink()
 */
class Employee extends app\models\Employee implements Linkable, Embeddable
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
    protected $shopClass = Shop::class;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'slug' => [
                'class' => Slug::class,
                'resourceName' => 'employee',
                'parentSlugRelation' => 'shop',
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
            'shop' => $this->getSelfLink() . '/shop',
        ]);
    }
    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return [
            'shop',
        ];
    }
}