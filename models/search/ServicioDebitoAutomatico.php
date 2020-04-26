<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ServicioDebitoAutomatico as ServicioDebitoAutomaticoModel;

/**
 * ServicioDebitoAutomatico represents the model behind the search form of `app\models\ServicioDebitoAutomatico`.
 */
class ServicioDebitoAutomatico extends ServicioDebitoAutomaticoModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_debitoautomatico', 'id_servicio'], 'integer'],
            [['tiposervicio', 'resultado_procesamiento', 'linea'], 'safe'],
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
        $query = ServicioDebitoAutomaticoModel::find();

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
            'id_debitoautomatico' => $this->id_debitoautomatico,
            'id_servicio' => $this->id_servicio,
        ]);

        $query->andFilterWhere(['like', 'tiposervicio', $this->tiposervicio])
            ->andFilterWhere(['like', 'resultado_procesamiento', $this->resultado_procesamiento])
            ->andFilterWhere(['like', 'linea', $this->linea]);

        return $dataProvider;
    }
}
