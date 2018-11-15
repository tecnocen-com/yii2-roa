<?php

class m130101_000002_shop extends \tecnocen\migrate\CreateTableMigration
{

    /**
     * @inhertidoc
     */
    public function getTableName()
    {
        return 'shop';
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
