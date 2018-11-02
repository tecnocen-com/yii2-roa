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
            'shop_id' => $this->normalKey(),
            'employee_id' => $this->normalKey(),
            'deleted' => $this->boolean(),
        ];
    }

    /**
     * @inhertidoc
     */
    public function foreignKeys()
    {
        return [
            'shop_id' => 'shop',
            'employee_id' => 'shop_employee',
        ];
    }
}
