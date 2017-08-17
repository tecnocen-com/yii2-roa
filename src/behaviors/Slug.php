<?php

namespace tecnocen\roa\behaviors;

use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * Behavior to handle slug componentes linked as parent-child relations.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 * @author Luis (Berkant) Campos <lcampos@artificesweb.com>
 * @author Alejandro (Seether69) Márquez <amarquez@solmipro.com>
 */
class Slug extends \yii\base\Behavior
{
    /**
     * @var callable a PHP callable which will determine if the logged
     * user has permission to access a resource record or any of its
     * chidren resources.
     *
     * It must have signature
     * ```php
     * function (array $queryParams): void
     *     throws \yii\web\HTTPException
     * {
     * }
     * ```
     */
    public $checkAccess;

    /**
     * @var string name of the parent relation of the `$owner`
     */
    public $parentSlugRelation;

    /**
     * @var string name of the resource
     */
    public $resourceName;

    /**
     * @var string|array name of the identifier attribute
     */
    public $idAttribute = 'id';

    /**
     * @var string separator to create the route for resources with multiple id
     * attributes.
     */
    public $idAttributeSeparator = '/';

    /**
     * @var string parentNotFoundMessage for not found exception when the parent
     * slug was not found
     */
    public $parentNotFoundMessage = '"{resourceName}" not found';

    /**
     * @var ActiveRecord parent record.
     */
    protected $parentSlug;

    /**
     * @var string url to resource
     */
    protected $resourceLink;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (empty($this->resourceName)) {
            throw new InvalidConfigException(
                self::class . '::$resourceName must be defined.'
            );
        }
        $this->idAttribute = (array) $this->idAttribute;
    }

    /**
     * Ensures the parent record is attached to the behavior.
     *
     * @param  ActiveRecord $owner
     * @param  bool $force whether to force finding the slug parent record
     * when `$parentSlugRelation` is defined
     */
    private function ensureSlug($owner, $force = false)
    {
        if (null === $this->parentSlugRelation) {
            $this->resourceLink = Url::to([$this->resourceName . '/'], true);
        } elseif ($force
            || $owner->isRelationPopulated($this->parentSlugRelation)
        ) {
            $this->populateSlugParent($owner);
        }
    }

    /**
     * This populates the slug to the parentSlug
     * @param  ActiveRecord $owner
     */
    private function populateSlugParent($owner)
    {
        $relation = $this->parentSlugRelation;
        $this->parentSlug = $owner->$relation;
        if (null === $this->parentSlug) {
            throw new NotFoundHttpException(
                strtr(
                    $this->parentNotFoundMessage,
                    [
                        '{resourceName}' => $this->parentSlugRelation,
                    ]
                )
            );
        }
        $this->resourceLink = $this->parentSlug->getSelfLink()
            . '/' . $this->resourceName;
    }

    /**
     * @return mixed value of the owner's identifier
     */
    public function getResourceRecordId()
    {
        $attributeValues = [];
        foreach ($this->idAttribute as $attribute) {
            $attributeValues[] = $this->owner->$attribute;
        }

        return implode($attributeValues, $this->idAttributeSeparator);
    }

    /**
     * @return string HTTP Url to the resource list
     */
    public function getResourceLink()
    {
        $this->ensureSlug($this->owner, true);

        return $this->resourceLink;
    }

    /**
     * @return string HTTP Url to self resource
     */
    public function getSelfLink()
    {
        $resourceRecordId = $this->getResourceRecordId();
        $resourceLink = $this->getResourceLink();

        return $resourceRecordId
            ? "$resourceLink/$resourceRecordId"
            : $resourceLink;
    }

    /**
     * @return array link to self resource and all the acumulated parent's links
     */
    public function getSlugLinks()
    {
        $this->ensureSlug($this->owner, true);
        $selfLinks = [
            'self' => $this->getSelfLink(),
            $this->resourceName . '_collection' => $this->resourceLink,
        ];
        if (null === $this->parentSlug) {
            return $selfLinks;
        }
        $parentLinks = $this->parentSlug->getSlugLinks();
        $parentLinks[$this->parentSlugRelation . '_record']
            = $parentLinks['self'];
        unset($parentLinks['self']);
        // preserve order
        return array_merge($selfLinks, $parentLinks);
    }

    /**
     * Determines if the logged user has permission to access a resource
     * record or any of its chidren resources.
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
}
