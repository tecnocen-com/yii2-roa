<?php

namespace app\api\models;

use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * Contract to filter and sort collections of `Employee` records.
 *
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class EmployeeSearch extends Employee implements \src\ResourceSearch
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
    public function search(array $params, $formName = '')
    {
        $this->load($params, $formName);
        if (!$this->validate()) {
            return null;
        }
        if (null === $this->shop) {
            throw new NotFoundHttpException('Unexistant shop path.');
        }
        $class = get_parent_class();
        return new ActiveDataProvider([
            'query' => $class::find()->andFilterWhere([
                    'shop_id' => $this->shop_id,
                ])
                ->andFilterWhere(['like', 'name', $this->name]),
        ]);
    }
}