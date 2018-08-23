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
            'path' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(32)->notNull()->unique(),
            'mime_type' => $this->string(32)->notNull()->unique(),
        ];
    }
}
