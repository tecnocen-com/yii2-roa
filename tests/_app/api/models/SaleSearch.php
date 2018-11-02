<?php

namespace app\api\models;

use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * Contract to filter and sort collections of `Sale` records.
 *
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class SaleSearch extends Sale implements \tecnocen\roa\ResourceSearch
{
    /**
     * @inhertidoc
     */
    public function rules()
    {
        return [
            [['shop_id','employee_id'], 'integer'],
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
        if (null === $this->shop) {
            throw new NotFoundHttpException('Unexistant shop path.');
        }
        $class = get_parent_class();
        return new ActiveDataProvider([
            'query' => $class::find()->andFilterWhere([
                    'shop_id' => $this->shop_id,
                    'employee_id' => $this->employee_id,
                ]),
        ]);
    }
}
