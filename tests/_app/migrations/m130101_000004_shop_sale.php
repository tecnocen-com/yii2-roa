<?php

class m130101_000004_shop_sale extends \tecnocen\migrate\CreateTableMigration
{

    /**
     * @inhertidoc
     */
    public function getTableName()
    {
        return 'shop_sale';
    }

    /**
     * @inhertidoc
     */
    public function columns()
    {
        return [
            'id' => $this->primaryKey(),
            'employee_id' => $this->normalKey(),
            'deleted' => $this->boolean()->notNull()->defaultValue(false),
        ];
    }

    /**
     * @inhertidoc
     */
    public function foreignKeys()
    {
        return [
            'employee_id' => 'shop_employee',
        ];
    }
}
