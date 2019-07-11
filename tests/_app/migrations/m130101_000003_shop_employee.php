<?php

class m130101_000003_shop_employee extends \tecnocen\migrate\CreateTableMigration
{

    /**
     * @inhertidoc
     */
    public function getTableName()
    {
        return 'shop_employee';
    }

    /**
     * @inhertidoc
     */
    public function columns()
    {
        return [
            'id' => $this->primaryKey(),
            'shop_id' => $this->normalKey(),
            'name' => $this->string(32)->notNull()->unique(),
            'deleted' => $this->boolean()->notNull()->defaultValue(false),
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
