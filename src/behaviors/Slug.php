<?php

namespace tecnocen\roa\behaviors;

use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * Behavior to handle slug componentes linked as parent-child relations.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 * @author Luis (Berkant) Campos <lcampos@artificesweb.com>
 */
class Slug extends \yii\base\Behavior
{
    /**
     * @var bool
     */
    public $checkAccess;

    /**
     * @var string Name of the parent relation
     */
    public $parentSlugRelation;

    /**
     * @var string Name of the resource
     */
    public $resourceName;

    /**
     * @var string Name of the id column
     */
    public $idAttribute = 'id';

    /**
     * @var string Name of the parent resource
     */
    protected $parentSlug;

    /**
     * @var string url to resource
     */
    protected $resourceLink;

    /**
     * Function to attach Slug
     * @param  object $owner
     */
    public function attach($owner)
    {
        parent::attach($owner);
        $this->ensureSlug($owner);
    }

    /**
     * Function that ensures the relation to parent
     * it can be forced
     * @param  object  $owner
     * @param  boolean $forceFind
     */
    private function ensureSlug($owner, $forceFind = false)
    {
        if (null === $this->parentSlugRelation) {
            $this->resourceLink = Url::to([$this->resourceName . '/'], true);
        } elseif ($forceFind 
            ||$owner->isRelationPopulated($this->parentSlugRelation)
        ) {
            $this->populateSlugParent($owner);
        }
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [ActiveRecord::EVENT_AFTER_FIND => 'afterFind'];
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->ensureSlug($this->owner, true);
    }

    /**
     * This populates the slug to the parentSlug
     * @param  object $owner
     */
    private function populateSlugParent($owner)
    {
        $relation = $this->parentSlugRelation;
        $this->parentSlug = $owner->$relation;
        $this->resourceLink = $this->parentSlug->selfLink
            . '/' . $this->resourceName;
    }

    /**
     * @inheritdoc
     */
    public function getResourceRecordId()
    {
        return $this->owner->getAttribute($this->idAttribute);
    }

    /**
     * @inheritdoc
     */
    public function getResourceLink()
    {
        return $this->resourceLink;
    }

    /**
     * @inheritdoc
     */
    public function getSelfLink()
    {
        return $this->resourceLink . '/' . $this->getResourceRecordId();
    }

    /**
     * @inheritdoc
     */
    public function getSlugLinks()
    {
        $this->ensureSlug($this->owner, true);
        $selfLinks = [
            'self' => $this->getSelfLink(),
            $this->resourceName . '_list' => $this->resourceLink,
        ];
        if (null === $this->parentSlug) {
            return $selfLinks;
        }
        $parentLinks = $this->parentSlug->getSlugLinks();
        $parentLinks['parent_' . $this->parentSlugRelation]
            = $parentLinks['self'];
        unset($parentLinks['self']);
        // preserve order
        return array_merge($selfLinks, $parentLinks);
    }

    /**
     * Checks the access to the parents
     * @param  Array $params
     */
    public function checkAccess($params)
    {
        $this->ensureSlug($this->owner, true);

        if (null !== $this->checkAccess) {
            call_user_func($this->checkAccess, $params);
        }
        if (null !== $this->parentSlug) {
            $this->parentSlug->checkAccess($params);
        }
    }


    /**
     * @inheritdoc
     */
    public function getSlugBehavior()
    {
        return $this;
    }
}
