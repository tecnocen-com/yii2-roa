<?php

namespace tecnocen\roa\urlRules;

use yii\base\Object as BaseObject;
use yii\web\NotFoundHttpException;

/**
 * Url rule that can call children rule when applicable.
 *
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
abstract class Composite extends \yii\web\CompositeUrlRule
{
    /**
     * @var boolean whether this rule must throw an `NotFoundHttpException` when
     * parse request fails.
     */
    public $strict = true;

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
        BaseObject::init();
    }

    /**
     * Determines if this rule must parse the request using the children rules
     * or return `false` inmediately.
     *
     * @return bool
     */
    abstract protected function isApplicable($route);

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
        $this->ensureRules();
        $result = parent::parseRequest($manager, $request);
        if ($result === false && $this->strict === true) {
            throw $this->createNotFoundException();
        }
        return $result;
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
     * @return NotFoundHttpException
     */
    public function createNotFoundException()
    {
        return new NotFoundHttpException($this->notFoundMessage);
    }
}
