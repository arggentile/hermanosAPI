<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CuotaConvenioPago;

/**
 * CuotaConvenioPagoSearch represents the model behind the search form of `app\models\CuotaConvenioPago`.
 */
class CuotaConvenioPagoSearch extends CuotaConvenioPago
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_conveniopago', 'nro_cuota', 'id_tiket'], 'integer'],
            [['fecha_establecida', 'estado'], 'safe'],
            [['monto', 'importe_abonado'], 'number'],
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
        $query = CuotaConvenioPago::find();

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
            'id_conveniopago' => $this->id_conveniopago,
            'fecha_establecida' => $this->fecha_establecida,
            'nro_cuota' => $this->nro_cuota,
            'monto' => $this->monto,
            'id_tiket' => $this->id_tiket,
            'importe_abonado' => $this->importe_abonado,
        ]);

        $query->andFilterWhere(['like', 'estado', $this->estado]);

        return $dataProvider;
    }
}
