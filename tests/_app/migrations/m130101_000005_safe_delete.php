<?php

class m130101_000005_safe_delete extends \tecnocen\migrate\CreateTableMigration
{
    /**
     * @inhertidoc
     */
    public function getTableName()
    {
        return 'safe_delete';
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
