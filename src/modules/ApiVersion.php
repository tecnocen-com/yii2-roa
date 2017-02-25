<?php

namespace tecnocen\roa\modules;

use DateTime;
use tecnocen\roa\controllers\ApiVersionController;
use yii\base\InvalidConfigException;

/**
 * Class to attach a version to an `ApiContainer` module.
 *
 * You can control the stability by setting the properties `$releaseDate`,
 * `$deprecationDate` and `$obsoleteDate`.
 *
 * The resources are declared using the `$resources` array property
 */
class ApiVersion extends \yii\base\Module
{
    const STABILITY_DEVELOPMENT = 'development';
    const STABILITY_STABLE = 'stable';
    const STABILITY_DEPRECATED = 'deprecated';
    const STABILITY_OBSOLETE = 'obsolete';

    /**
     * @var string date in Y-m-d format for the date at which this version
     * became stable
     */
    public $releaseDate;

    /**
     * @var string date in Y-m-d format for the date at which this version
     * became deprecated
     */
    public $deprecationDate;

    /**
     * @var string date in Y-m-d format for the date at which this version
     * became obsolete
     */
    public $obsoleteDate;

    /**
     * @var string the stability level
     */
    protected $stability = self::STABILITY_DEVELOPMENT;

    /**
     * @return string the stability defined for this version.
     */
    public function getStability()
    {
        return $this->stability;
    }

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'index';

    /**
     * @inheritdoc
     */
    public $controllerMap = ['index' => ApiVersionController::class];

    /**
     * @var string[] list of 'patternRoute' => 'resource' pairs to connect a
     * route to a resource. if no key is used, then the value will be the
     * pattern too.
     *
     * ```php
     * [
     *     'profile',
     *     'profile/image' => 'profile-image',
     *     'profile/image/<image_id:[\d]+>/comment' => 'profile-image-comment',
     *     'timeline',
     *     'post',
     *     'post/<post_id:[\d]+>/reply' => 'post-reply',
     * ]
     * ```
     */
    public $resources = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($releaseTime = $this->calcTime($this->releaseDate)
            && $releaseTime <= ($now = time())
        ) {
            if ($deprecationTime = $this->calcTime($this->deprecationDate)
                && $obsoleteTime = $this->calcTime($this->obsoleteDate)
            ) {
                if ($obsoleteTime < $deprecationTime) {
                    throw new InvalidConfigException(
                        'The `obsoleteDate` must not be earlier than `deprecationDate`'
                    );
                }
                if ($deprecationTime < $releaseTime) {
                    throw new InvalidConfigException(
                        'The `deprecationDate` must not be earlier than `releaseDate`'
                    );
                }

                if ($obsoleteTime > $now) {
                    $this->stability = self::STABILITY_OBSOLETE;
                } elseif ($deprecationTime > $now) {
                    $this->stability = self::STABILITY_DEPRECATED;
                } else {
                    $this->stability = self::STABILITY_STABLE;
                }
            } else {
                $this->stability = self::STABILITY_STABLE;
            }
        }
    }

    /**
     * @param string date in 'Y-m-d' format
     * @return integer unix timestamp
     */
    private function calcTime($date)
    {
         if ($date === null) {
             return null;
         }
         if (false === ($dt = DateTime::createFromFormat('Y-m-d', $date))) {
             throw new InvalidConfigException(
                 'Dates must use the "Y-m-d" format.'
             );
         }

         return $dt->getTimestamp();
    }
}
