<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ServicioOfrecido;

/**
 * ServicioOfrecidoSearch represents the model behind the search form of `app\models\ServicioOfrecido`.
 */
class ServicioOfrecidoSearch extends ServicioOfrecido
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_categoriaservicio'], 'integer'],
            [['nombre', 'descripcion', 'fecha_inicio', 'fecha_fin', 'fecha_vencimiento'], 'safe'],
            [['importe', 'importe_hijoprofesor'], 'number'],
            [['devengamiento_automatico', 'activo'], 'boolean'],
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
        $query = ServicioOfrecido::find();

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
            'id_categoriaservicio' => $this->id_categoriaservicio,
            'importe' => $this->importe,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'devengamiento_automatico' => $this->devengamiento_automatico,
            'activo' => $this->activo,
            'importe_hijoprofesor' => $this->importe_hijoprofesor,
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion]);

        return $dataProvider;
    }
}
