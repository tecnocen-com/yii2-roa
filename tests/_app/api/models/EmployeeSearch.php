<?php

namespace app\api\models;

use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * Contract to filter and sort collections of `Employee` records.
 *
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class EmployeeSearch extends Employee implements \tecnocen\roa\ResourceSearch
{
    /**
     * @inhertidoc
     */
    public function rules()
    {
        return [
            [['shop_id'], 'integer'],
            [['name'], 'string'],
        ];
    }
    /**
     * @inhertidoc
     */
    public function search(
        array $params,
        ?string $formName = ''
    ): ?ActiveDataProvider {
        $this->load($params, $formName);
        if (!$this->validate()) {
            return null;
        }
        if (null === $this->shop || $this->shop->deleted) {
            throw new NotFoundHttpException('Unexistant shop path.');
        }
        $class = get_parent_class();

        return new ActiveDataProvider([
            'query' => $class::find()
                ->andFilterWhere([
                    'shop_id' => $this->shop_id,
                ])
                ->andFilterWhere(['like', 'name', $this->name]),
        ]);
    }
}
