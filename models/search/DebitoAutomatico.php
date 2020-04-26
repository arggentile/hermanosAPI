<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DebitoAutomatico as DebitoAutomaticoModel;

/**
 * DebitoAutomatico represents the model behind the search form of `app\models\DebitoAutomatico`.
 */
class DebitoAutomatico extends DebitoAutomaticoModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'registros_enviados', 'registros_correctos'], 'integer'],
            [['nombre', 'banco', 'tipo_archivo', 'fecha_creacion', 'fecha_procesamiento', 'inicio_periodo', 'fin_periodo', 'fecha_debito'], 'safe'],
            [['procesado'], 'boolean'],
            [['saldo_enviado', 'saldo_entrante'], 'number'],
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
        $query = DebitoAutomaticoModel::find();

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
            'fecha_creacion' => $this->fecha_creacion,
            'fecha_procesamiento' => $this->fecha_procesamiento,
            'inicio_periodo' => $this->inicio_periodo,
            'fin_periodo' => $this->fin_periodo,
            'fecha_debito' => $this->fecha_debito,
            'procesado' => $this->procesado,
            'registros_enviados' => $this->registros_enviados,
            'registros_correctos' => $this->registros_correctos,
            'saldo_enviado' => $this->saldo_enviado,
            'saldo_entrante' => $this->saldo_entrante,
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'banco', $this->banco])
            ->andFilterWhere(['like', 'tipo_archivo', $this->tipo_archivo]);

        return $dataProvider;
    }
}
