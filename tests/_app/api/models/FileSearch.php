<?php

namespace app\api\models;

use yii\data\ActiveDataProvider;

/**
 * Contract to filter and sort collections of `File` records.
 *
 * @author Carlos (neverabe) Llamosas <carlos@tecnocen.com>
 */
class FileSearch extends File implements \tecnocen\roa\ResourceSearch
{
    /**
     * @inhertidoc
     */
    public function rules()
    {
        return [
            [['name','path','mime_type'], 'string'],
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
            'query' => $class::find()
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'path', $this->path])
            ->andFilterWhere(['like', 'mime_type', $this->mime_type])
        ]);
    }
}
