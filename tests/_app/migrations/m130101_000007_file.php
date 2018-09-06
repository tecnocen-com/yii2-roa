<?php

class m130101_000007_file extends \tecnocen\migrate\CreateTableMigration
{
    
    /**
     * @inhertidoc
     */
    public function getTableName()
    {
        return 'file';
    }

    /**
     * @inhertidoc
     */
    public function columns()
    {
        return [
            'id' => $this->primaryKey(),
            'path' => $this->string(512)->notNull(),
            'name' => $this->string(256)->notNull(),
            'mime_type' => $this->string(32)->notNull(),
        ];
    }
}
