<?php

class m130101_000002_shop extends \tecnocen\migrate\CreateTableMigration
{
    public function getTableName()
    {
        return 'shop';
    }

    public function columns()
    {
        return [
            'id' => $this->primaryKey(),
            'name' => $this->string(32)->notNull()->unique(),
        ];
    }
}
