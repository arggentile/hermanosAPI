<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\GrupoFamiliar;

/**
 * GrupoFamiliarSearch represents the model behind the search form of `app\models\GrupoFamiliar`.
 */
class GrupoFamiliarSearch extends GrupoFamiliar
{
    public $responsable;
    //filtro para descartar familias que tengan todos los alumnos
    //inactivos
    public $familias_activas; 
    public $familias_inactivas; 
    
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_pago_asociado'], 'integer'],
            [['apellidos', 'descripcion', 'folio', 'cbu_cuenta', 'nro_tarjetacredito', 'tarjeta_banco', 'prestador_tarjeta'], 'safe'],
            [['responsable','familias_activas','familias_inactivas'],'safe'],
            
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
        $query = GrupoFamiliar::find()->distinct();
        $query->joinWith(['responsables r','responsables.persona p','alumnos a']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                    'defaultOrder'=>['folio'=>SORT_ASC]
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        if($this->familias_activas=='1' || $this->familias_activas==1){
            $query->andFilterWhere(['a.activo' => true]);            
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'id_pago_asociado' => $this->id_pago_asociado,
        ]);

        $query->andFilterWhere(['like', 'apellidos', $this->apellidos])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'folio', $this->folio])
            ->andFilterWhere(['like', 'cbu_cuenta', $this->cbu_cuenta])
            ->andFilterWhere(['like', 'nro_tarjetacredito', $this->nro_tarjetacredito])
            ->andFilterWhere(['like', 'tarjeta_banco', $this->tarjeta_banco])
            ->andFilterWhere(['like', 'prestador_tarjeta', $this->prestador_tarjeta]);

        return $dataProvider;
    }
}
