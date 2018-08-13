<?php

namespace tecnocen\roa;

interface ResourceSearch
{
    /**
     * Creates a data provider to search records in a resource.
     *
     * @param array $params search parameters.
     * @param ?string $formName the name of the form to load into the model
     * @return ?\yii\data\DataProviderInterface
     */
    public function search(array $params, $formName = null);
}
