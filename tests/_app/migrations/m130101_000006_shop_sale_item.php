<?php

class m130101_000006_shop_sale_item extends \tecnocen\migrate\CreateTableMigration
{
    public $defaultOnDelete = 'RESTRICT';
    
    /**
     * @inhertidoc
     */
    public function getTableName()
    {
        return 'shop_sale_item';
    }

    /**
     * @inhertidoc
     */
    public function columns()
    {
        return [
            'id' => $this->primaryKey(),
            'item_id' => $this->normalKey(),
            'sale_id' => $this->normalKey(),
        ];
    }

    /**
     * @inhertidoc
     */
    public function foreignKeys()
    {
        return [
            'item_id' => 'item',
            'sale_id' => 'shop_sale',
        ];
    }
}
