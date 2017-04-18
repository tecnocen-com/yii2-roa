<?php

namespace tecnocen\roa\behaviors;

use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

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
        if (null !== $this->parentSlugRelation) {
            if ($owner->isRelationPopulated($this->parentSlugRelation)) {
                $this->populateSlugParent($owner);
            }
        } else {
            $this->resourceLink = Url::to([$this->resourceName . '/'], true);
        }
    }

    public function event()
    {
        return [ActiveRecord::EVENT_AFTER_FIND => 'afterFind'];
    }

    public function afterFind()
    {
        $relation = $this->parentSlugRelation;
        $this->owner->$relation;
        $this->populateSlugParent($this->owner);
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
        $selfLinks = [
            'self' => $this->getSelfLink(),
            $this->resourceName => $this->resourceLink,
        ];
        if (null === $this->parentSlug) {
            return $selfLinks;
        }
        $parentLinks = $this->parentSlug->getSelfLink();
        $pentLinks[$this->parentSlugRelation] = $parentLinks['self']; 
        unset($links['self']);
        return array_merge($selfLinks, $parentLinks);
    }

    public function checkAccess($params)
    {
         if (null !== $this->parentSlugRelation) {
             $this->populateSlugParent($this->owner);
             if (null === $this->parentSlug) {
                 throw new NotFoundHttpException(
                     "{$this->parentSlugRelation} not found."
                 );
             }
        }
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
