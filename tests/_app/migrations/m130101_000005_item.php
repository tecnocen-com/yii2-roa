<?php

class m130101_000005_item extends \tecnocen\migrate\CreateTableMigration
{

    /**
     * @inhertidoc
     */
    public function getTableName()
    {
        return 'item';
    }

    /**
     * @inhertidoc
     */
    public function columns()
    {
        return [
            'id' => $this->primaryKey(),
            'name' => $this->string(32)->notNull()->unique(),
            'deleted' => $this->boolean()->notNull()->defaultValue(false),
        ];
    }
}
