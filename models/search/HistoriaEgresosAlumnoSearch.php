<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\HistoriaEgresosAlumno;

/**
 * HistoriaEgresosAlumnoSearch represents the model behind the search form of `app\models\HistoriaEgresosAlumno`.
 */
class HistoriaEgresosAlumnoSearch extends HistoriaEgresosAlumno
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_alumno', 'id_division_actual', 'id_division_egreso'], 'integer'],
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
        $query = HistoriaEgresosAlumno::find();

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
            'id_alumno' => $this->id_alumno,
            'id_division_actual' => $this->id_division_actual,
            'id_division_egreso' => $this->id_division_egreso,
        ]);

        return $dataProvider;
    }
}
