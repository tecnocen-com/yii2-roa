<?php

namespace app\api\models;

use yii\data\ActiveDataProvider;

/**
 * Contract to filter and sort collections of `Shop` records.
 *
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class ShopSearch extends Shop implements \tecnocen\roa\ResourceSearch
{
    /**
     * @inhertidoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
        ];
    }
    /**
     * @inhertidoc
     */
    public function search(array $params, $formName = '')
    {
        $this->load($params, $formName);
        if (!$this->validate()) {
            return null;
        }
        $class = get_parent_class();

        return new ActiveDataProvider([
            'query' => $class::find()->andFilterWhere(
                ['like', 'name', $this->name]
            ),
        ]);
    }
}
