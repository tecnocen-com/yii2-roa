<?php

namespace tecnocen\roa\modules;

use DateTime;
use tecnocen\roa\controllers\ApiVersionController;
use yii\base\InvalidConfigException;

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
     * @var string[]
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
            if ($deprecatedTime = $this->calcTime($this->deprecatedDate)
                && $obsoleteTime = $this->calcTime($this->obsoleteDate)
            ) {
                if ($obsoleteTime < $deprecatedTime) {
                    throw new InvalidConfigException(
                        'The `obsoleteDate` must not be earlier than `deprecatedDate`'
                    );
                }
                if ($deprecatedTime < $releaseTime) {
                    throw new InvalidConfigException(
                        'The `deprecatedDate` must not be earlier than `releaseDate`'
                    );
                }

                if ($obsoleteTime > $now) {
                    $this->stability = self::STABILITY_OBSOLETE;
                } elseif ($deprecatedTime > $now) {
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
