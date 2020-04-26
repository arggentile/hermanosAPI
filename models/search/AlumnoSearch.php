<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Alumno;

/**
 * AlumnoSearch represents the model behind the search form of `app\models\Alumno`.
 */
class AlumnoSearch extends Alumno
{
 
    public $familia;
    public $establecimiento;
    public $inicial;
    //public $con_servicios;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_persona', 'id_grupofamiliar', 'id_divisionescolar'], 'integer'],
            [['fecha_ingreso', 'nro_legajo', 'familia', 'establecimiento','inicial','hijo_profesor'], 'safe'],
            [['activo','egresado'], 'boolean'],      
            
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
    public function search($params, $persona, $conPagination=true)
    {
        $query = Alumno::find();
        $query->alias("a");
       
        $query->joinWith(['persona p','grupofamiliar f','divisionescolar d']);

        // add conditions that should always apply here

        if($conPagination){
            $dataProvider = new ActiveDataProvider([
                'query' => $query,   
                'sort'=>[
                    'defaultOrder'=>['apellido'=>SORT_ASC, 'nombre'=>SORT_ASC]
                ]
            ]);
        }else{
            $dataProvider = new ActiveDataProvider([
                'query' => $query,   
                'pagination'=>false,
                'sort'=>[
                    'defaultOrder'=>['apellido'=>SORT_ASC, 'nombre'=>SORT_ASC]
                ]
            ]);
        }
        
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        
        $query->andFilterWhere([
            'id' => $this->id,
            'id_persona' => $this->id_persona,
            'id_grupofamiliar' => $this->id_grupofamiliar,
            'id_divisionescolar' => $this->id_divisionescolar,
            'fecha_ingreso' => $this->fecha_ingreso,
            'activo' => $this->activo,           
            'hijo_profesor'=> $this->hijo_profesor,
        ]);
        
    
        $query->andFilterWhere(['=','egresado', $this->egresado]);

        if(!empty($this->establecimiento)){
            // grid filtering conditions
            $query->andFilterWhere([
                'd.id_establecimiento' => $this->establecimiento,
            ]);
        }

        $query->andFilterWhere(['like', 'nro_legajo', $this->nro_legajo]);        
        $query->andFilterWhere(['like', 'f.apellidos', $this->familia]);        
        $query->andFilterWhere(['like', 'p.apellido', $persona->apellido]);
        $query->andFilterWhere(['like', 'p.nombre', $persona->nombre]);        
        $query->andFilterWhere(['like', 'p.nro_documento', $persona->nro_documento]);
        
        
        $dataProvider->sort->attributes['apellido'] = [        
            'asc' => ['p.apellido' => SORT_ASC],
            'desc' => ['p.apellido' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['documento'] = [        
            'asc' => ['p.nro_documento' => SORT_ASC],
            'desc' => ['p.nro_documento' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['nombre'] = [        
            'asc' => ['p.nombre' => SORT_ASC],
            'desc' => ['p.nombre' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['familia'] = [        
            'asc' => ['f.apellidos' => SORT_ASC],
            'desc' => ['f.apellidos' => SORT_DESC],
        ];
                
        return $dataProvider;        
    }
}
