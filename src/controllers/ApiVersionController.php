<?php

namespace tecnocen\roa\controllers;

class ApiVersionController extends
{
    /**
     * List of all the resources available for the parent module api version.
     *
     * @return string[]
     */
    public function indexAction()
    {
        return $this->module->resources;
    }
}
