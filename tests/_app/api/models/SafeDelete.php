<?php

namespace app\api\models;

use tecnocen\roa\behaviors\Curies;
use tecnocen\roa\behaviors\Slug;
use tecnocen\roa\hal\Embeddable;
use tecnocen\roa\hal\EmbeddableTrait;
use yii\web\Linkable;
use yii\web\NotFoundHttpException;
/**
 * ROA contract to handle SafeDelete records.
 *
 * @method string[] getSlugLinks()
 * @method string getSelfLink()
 */
class SafeDelete extends \app\models\SafeDelete implements Linkable, Embeddable
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
    protected $safeDeleteChildClass = SafeDeleteChild::class;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'slug' => [
                'class' => Slug::class,
                'resourceName' => 'safe-delete',
                'checkAccess' => function ($params) {
                    if (isset($params['safe_delete_id'])
                        && $this->id != $params['safe_delete_id']
                    ) {
                        throw new NotFoundHttpException(
                            'Safe Delete not associated to element.'
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
            'child' => $this->getSelfLink() . '/child',
        ]);
    }
    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return ['safeDeleteChild'];
    }
}