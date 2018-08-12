<?php

namespace app\api\models;

use src\behaviors\Curies;
use src\behaviors\Slug;
use src\Embeddable;
use src\EmbeddableTrait;
use yii\web\Linkable;
use yii\web\NotFoundHttpException;
/**
 * ROA contract to handle shop records.
 *
 * @method string[] getSlugLinks()
 * @method string getSelfLink()
 */
class Shop extends app\models\Shop implements Linkable, Embeddable
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
    protected $employeeClass = Employee::class;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'slug' => [
                'class' => Slug::class,
                'resourceName' => 'shop',
                'checkAccess' => function ($params) {
                    if (isset($params['shop_id'])
                        && $this->id != $params['shop_id']
                    ) {
                        throw new NotFoundHttpException(
                            'Shop not associated to element.'
                        );
                    }
                },
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
            'employee' => $this->getSelfLink() . '/employee',
        ]);
    }
    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['employees'];
    }
}