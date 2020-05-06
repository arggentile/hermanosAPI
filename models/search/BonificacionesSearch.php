<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Bonificaciones;

/**
 * BonificacionesSearch represents the model behind the search form of `app\models\Bonificaciones`.
 */
class BonificacionesSearch extends Bonificaciones
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cantidad_hermanos'], 'integer'],
            [['descripcion'], 'safe'],
            [['valor'], 'number'],
            [['activa', 'automatica'], 'boolean'],
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
        $query = Bonificaciones::find();

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
            'activa' => $this->activa,
            'automatica' => $this->automatica,
            'cantidad_hermanos' => $this->cantidad_hermanos,
        ]);

        $query->andFilterWhere(['like', 'descripcion', $this->descripcion]);

        return $dataProvider;
    }
}
