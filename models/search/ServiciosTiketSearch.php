<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ServiciosTiket;

/**
 * ServiciosTiketSearch represents the model behind the search form of `app\models\ServiciosTiket`.
 */
class ServiciosTiketSearch extends ServiciosTiket
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_tiket', 'id_servicio', 'tiposervicio'], 'integer'],
            [['monto_abonado'], 'number'],
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
        $query = ServiciosTiket::find();

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
            'id_tiket' => $this->id_tiket,
            'id_servicio' => $this->id_servicio,
            'tiposervicio' => $this->tiposervicio,
            'monto_abonado' => $this->monto_abonado,
        ]);

        return $dataProvider;
    }
}
