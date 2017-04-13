<?php

namespace tecnocen\roa\behaviors;

use yii\helpers\Url;

class Slug extends \yii\base\Behavior
{
    public $parentSlugRelation;

    public $resourceName;

    public $idAttribute = 'id';

    protected $parentSlug;

    protected $resourceLink;

    public function attach($owner)
    {
        parent::attach($owner);
        if (null !== ($relation = $this->parentSlugRelation)
            && null !== ($this->parentSlug = $owner->$relation))
        ) {
            $this->resourceLink = $this->parentSlug->resourceLink
                . '/' . $this->resourceName;
        } else {
            $this->resourceLink = Url::to([$this->resourceName . '/'], true);
        }
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
