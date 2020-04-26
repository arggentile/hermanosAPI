<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ConvenioPago;

/**
 * ConvenioPagoSearch represents the model behind the search form of `app\models\ConvenioPago`.
 */
class ConvenioPagoSearch extends ConvenioPago
{
    public $mifamilia;
    public $adeudan;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_familia'], 'integer'],
            [['nombre', 'fecha_alta', 'descripcion'], 'safe'],
            [['saldo_pagar'], 'number'],
            [['deb_automatico', 'con_servicios'], 'boolean'],
            [['mifamilia', 'adeudan'], 'safe'],
            
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
        $query = ConvenioPago::find();
        $query->joinWith(['familia', 'cuotaConvenioPagos']);
        
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
        'defaultOrder' => [
            'id' => SORT_DESC,
        ]
    ],
            
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
            'fecha_alta' => $this->fecha_alta,
            'id_familia' => $this->id_familia,
            'saldo_pagar' => $this->saldo_pagar,
            'deb_automatico' => $this->deb_automatico,            
        ]);
        
        $query->andFilterWhere(['=','con_servicios', $this->con_servicios]);
        
        if($this->adeudan=='1'){
            $findCuotas = \app\models\CuotaConvenioPago::find()->select('id_conveniopago')->where("id_estado=". \app\models\EstadoServicio::ID_ABIERTA)->asArray()->all();
            $findCuotas = \yii\helpers\ArrayHelper::getColumn($findCuotas, 'id_conveniopago');
            $query->andFilterWhere(['in', \app\models\ConvenioPago::tableName() . '.id', $findCuotas]);
        }            
        elseif($this->adeudan=='0'){
            $findCuotas = \app\models\CuotaConvenioPago::find()->select('id_conveniopago')->where("id_estado=". \app\models\EstadoServicio::ID_ABIERTA)->asArray()->all();
            $findCuotas = \yii\helpers\ArrayHelper::getColumn($findCuotas, 'id_conveniopago');
            $query->andFilterWhere(['not in', \app\models\ConvenioPago::tableName() . '.id', $findCuotas]);    
        }
            
            
        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion]);

        $query->andFilterWhere(['like', \app\models\GrupoFamiliar::tableName() . '.apellidos', $this->mifamilia]);
        
        return $dataProvider;
    }
}
