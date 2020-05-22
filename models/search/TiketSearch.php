<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tiket;

/**
 * TiketSearch represents the model behind the search form of `app\models\Tiket`.
 */
class TiketSearch extends Tiket
{
    public $fecha_inicio_desde;
    public $fecha_inicio_hasta;
    public $informada;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_tipopago', 'id_cliente'], 'integer'],
            [['nro_tiket', 'fecha_tiket', 'fecha_pago', 'detalles'], 'safe'],
            [['importe'], 'number'],
            [['fecha_inicio_desde','fecha_inicio_hasta','informada'], 'safe'],
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
        $query = Tiket::find();
        $query->joinWith(['miFactura fac']);

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
            'fecha_tiket' => $this->fecha_tiket,
            'id_tipopago' => $this->id_tipopago,
            'importe' => $this->importe,
            'fecha_pago' => $this->fecha_pago,
            'id_cliente' => $this->id_cliente,
        ]);

        $query->andFilterWhere(['like', 'nro_tiket', $this->nro_tiket])
            ->andFilterWhere(['like', 'detalles', $this->detalles]);

        //filtro fechas
        if(!empty($this->fecha_inicio_desde) && !empty($this->fecha_inicio_hasta)){
           $query->andFilterWhere(['between', 'fecha_pago', date('Y-m-d', strtotime($this->fecha_inicio_desde)), date('Y-m-d 23:59:59', strtotime($this->fecha_inicio_hasta))]); 
        }
        if($this->informada=='1' || $this->informada==1){
           $query->andFilterWhere(['fac.informada'=>'1']); 
        }elseif($this->informada=='0' || $this->informada==0){
           $query->andFilterWhere(['fac.informada'=>'0']); 
        }
        return $dataProvider;
    }
}
