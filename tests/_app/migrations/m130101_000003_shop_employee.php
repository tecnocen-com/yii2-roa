<?php

class m130101_000003_shop_employee extends \tecnocen\migrate\CreateTableMigration
{
    public function getTableName()
    {
        return 'shop_employee';
    }

    public function columns()
    {
        return [
            'id' => $this->primaryKey(),
            'shop_id' => $this->normalKey(),
            'name' => $this->string(32)->notNull()->unique(),
        ];
    }

    /**
     * @inhertidoc
     */
    public function foreignKeys()
    {
        return ['shop_id' => 'shop'];
    }
}
