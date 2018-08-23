<?php

class m130101_000006_safe_delete_child extends \tecnocen\migrate\CreateTableMigration
{
    
    public $defaultOnDelete = 'RESTRICT';
    /**
     * @inhertidoc
     */
    public function getTableName()
    {
        return 'safe_delete_child';
    }

    /**
     * @inhertidoc
     */
    public function columns()
    {
        return [
            'id' => $this->primaryKey(),
            'safe_delete_id' => $this->normalKey(),
        ];
    }

    /**
     * @inhertidoc
     */
    public function foreignKeys()
    {
        return ['safe_delete_id' => 'safe_delete'];
    }
}
