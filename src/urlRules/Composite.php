<?php

namespace tecnocen\roa\urlRules;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use yii\web\UrlManager;
use yii\web\UrlNormalizer;

/**
 * Url rule that can call children rule when applicable.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
abstract class Composite extends \yii\web\CompositeUrlRule
{
    /**
     * @var bool whether this rule must throw an `NotFoundHttpException` when
     * parse request fails.
     */
    public $strict = true;

    /**
     * @var UrlNormalizer|null
     */
    public $normalizer = null;

    /**
     * @var string message used to create the `NotFoundHttpException` when
     * `$strict` equals `true` and no children rules could parse the request.
     */
    public $notFoundMessage = 'Unknown route.';

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (is_array($this->normalizer)) {
            $this->normalizer = Yii::createObject(array_merge(
                ['class' => UrlNormalizer::class],
                $this->normalizer
            ));
        }
        if (!empty($this->normalizer)
            && !$this->normalizer instanceof UrlNormalizer
        ) {
            throw new InvalidConfigException(
                'Invalid config for `normalizer`.'
            );
        }
    }

    /**
     * Determines if this rule must parse the request using the children rules
     * or return `false` inmediately.
     *
     * @param string $route
     * @return bool
     */
    abstract protected function isApplicable(string $route): bool;

    /**
     * Ensures that `$rules` property is set
     */
    private function ensureRules()
    {
        if (empty($this->rules)) {
            $this->rules = $this->createRules();
        }
    }

    /**
     * @inheritdoc
     */
    public function parseRequest($manager, $request)
    {
        // only parse rules applicable rules
        if (!$this->isApplicable($request->pathInfo)) {
            return false;
        }
        $normalized = false;
        if ($this->hasNormalizer($manager)) {
            $request->pathInfo = $this->getNormalizer($manager)
                ->normalizePathInfo(
                    $request->pathInfo,
                    '',
                    $normalized
                );
        }
        $this->ensureRules();
        $result = parent::parseRequest($manager, $request);
        if ($result === false && $this->strict === true) {
            throw $this->createNotFoundException();
        }

        return $normalized
            ? $this->getNormalizer($manager)->normalizeRoute($result)
            : $result;
    }

    /**
     * @inheritdoc
     */
    public function createUrl($manager, $route, $params)
    {
        // only parse rules applicable rules
        if (!$this->isApplicable($route)) {
            return false;
        }
        $this->ensureRules();

        return parent::createUrl($manager, $route, $params);
    }

    /**
     * @param UrlManager $manager the URL manager
     * @return bool
     */
    protected function hasNormalizer($manager): bool
    {
        return null !== $this->getNormalizer($manager);
    }

    /**
     * @param UrlManager $manager the URL manager
     * @return ?UrlNormalizer
     */
    protected function getNormalizer(UrlManager $manager): ?UrlNormalizer
    {
        if ($this->normalizer === null
            && $manager->normalizer instanceof UrlNormalizer
        ) {
            return $manager->normalizer;
        }

        return $this->normalizer;
    }

    /**
     * @return NotFoundHttpException
     */
    protected function createNotFoundException()
    {
        return new NotFoundHttpException($this->notFoundMessage);
    }
}
