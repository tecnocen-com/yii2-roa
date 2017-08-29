<?php

namespace tecnocen\roa\openApi\schema;

/**
 * Interface to generate OpenApi 3.0 schemas.
 */
interface GeneratorInterface
{
    /**
     * Generates schemas differentiated by the defined property name.
     *
     * @return array OpenApi3.0 objects schemas
     */
    public function generateSchemas();
}
