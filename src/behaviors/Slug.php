<?php

namespace tecnocen\roa\behaviors;

use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * Behavior to handle slug componentes linked as parent-child relations.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 * @author Luis Campos <lcampos@artificesweb.com>
 */
class Slug extends \yii\base\Behavior
{
    public $checkAccess;

    public $parentSlugRelation;

    public $resourceName;

    public $idAttribute = 'id';

    protected $parentSlug;

    protected $resourceLink;

    public function attach($owner)
    {
        parent::attach($owner);
        $this->ensureSlug($owner);
    }
    
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

    public function event()
    {
        return [ActiveRecord::EVENT_AFTER_FIND => 'afterFind'];
    }

    public function afterFind()
    {
        $this->ensureSlug($this->owner, true);
    }

    private function populateSlugParent($owner)
    {
        $relation = $this->parentSlugRelation;
        $this->parentSlug = $owner->$relation;
        $this->resourceLink = $this->parentSlug->selfLink
            . '/' . $this->resourceName;
    }

    public function getResourceRecordId()
    {
        return $this->owner->getAttribute($this->idAttribute);
    }

    public function getResourceLink()
    {
        return $this->resourceLink;
    }

    public function getSelfLink()
    {
        return $this->resourceLink . '/' . $this->getResourceRecordId();
    }

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

    public function getSlugBehavior()
    {
        return $this;
    }
}
