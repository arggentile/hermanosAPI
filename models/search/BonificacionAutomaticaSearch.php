<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BonificacionAutomatica;

/**
 * BonificacionAutomaticaSearch represents the model behind the search form of `app\models\BonificacionAutomatica`.
 */
class BonificacionAutomaticaSearch extends BonificacionAutomatica
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cantidad_hemanos'], 'integer'],
            [['descripcion'], 'safe'],
            [['valor'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BonificacionAutomatica::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'valor' => $this->valor,
            'cantidad_hemanos' => $this->cantidad_hemanos,
        ]);

        $query->andFilterWhere(['like', 'descripcion', $this->descripcion]);

        return $dataProvider;
    }
}
