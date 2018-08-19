<?php

class m130101_000004_soft_delete extends \tecnocen\migrate\CreateTableMigration
{
    /**
     * @inhertidoc
     */
    public function getTableName()
    {
        return 'soft_delete';
    }

    /**
     * @inhertidoc
     */
    public function columns()
    {
        return [
            'id' => $this->primaryKey(),
            'deleted' => $this->boolean(),
        ];
    }
}
