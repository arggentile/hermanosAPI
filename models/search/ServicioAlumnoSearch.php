<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ServicioAlumno;

use Yii;
/**
 * ServicioAlumnoSearch represents the model behind the search form of `app\models\ServicioAlumno`.
 */
class ServicioAlumnoSearch extends ServicioAlumno
{
    
    //variables de filtros especificos
    public $apellidoAlumno, $nombreAlumno, $documentoAlumno;
    public $apellidoFamilia, $folioFamilia, $familia;
    public $servicio_ofrecido;
    public $establecimiento, $division_escolar;
    
    /**
     * {@inheritdoc}
     */
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_servicio', 'id_alumno'], 'integer'],
            [['id_estado','fecha_otorgamiento', 'fecha_cancelamiento','familia','folioFamilia','apellidoFamilia','division_escolar','servicio_ofrecido','establecimiento'], 'safe'],
            [['importe_servicio', 'importe_descuento', 'importe_abonado'], 'number'],           
            [['apellidoAlumno', 'nombreAlumno','documentoAlumno'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        
        $session = Yii::$app->session;
        $session->remove('serviciosalumnos');  
        
        $query = ServicioAlumno::find();
        $query->alias("s"); 
       
        $query->joinWith(['alumno a', 'alumno.divisionescolar div', 'alumno.persona p', 'alumno.grupofamiliar f']);

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
            's.id' => $this->id,
            's.id_servicio' => $this->id_servicio,
            'id_alumno' => $this->id_alumno,
            'fecha_otorgamiento' => $this->fecha_otorgamiento,
            'fecha_cancelamiento' => $this->fecha_cancelamiento,
            'importe_servicio' => $this->importe_servicio,
            'importe_descuento' => $this->importe_descuento,
            'importe_abonado' => $this->importe_abonado,   
            'id_estado' => $this->id_estado,  
        ]);
        
            //datos de familia
        $query->andFilterWhere(['like', 'f.apellidos', $this->apellidoFamilia]);
        $query->andFilterWhere(['like', 'f.folio', $this->folioFamilia]);  
        //datos de alumno
        $query->andFilterWhere(['like', 'p.apellido', $this->apellidoAlumno]);
        $query->andFilterWhere(['like', 'p.nombre', $this->nombreAlumno]);        
        $query->andFilterWhere(['like', 'p.nro_documento', $this->documentoAlumno]);
        
        if(!empty($this->division_escolar)){
            $query->andFilterWhere([
                'a.id_divisionescolar' => $this->division_escolar,
            ]);
        }
        
        if(!empty($this->establecimiento)){
            $query->andFilterWhere([
                'div.id_establecimiento' => $this->establecimiento,
            ]);
        }
        
        if(!empty($this->familia)){
            $query->andFilterWhere([
                'a.id_grupofamiliar' => $this->familia,
            ]);
        }
        
        
        
        $session->set('serviciosalumnos', $query->createCommand()->getRawSql());
        return $dataProvider;
    }
    
    
//    public function search($params)
//    {
//      
//        
//        
//       
//        $query->joinWith(['alumno a','alumno.divisionescolar div','alumno.persona p','alumno.grupofamiliar f','servicio so',]);
//

//        if(!empty($this->familia)){
//            /*$query->andFilterWhere(['or',
//                ['like','f.apellidos',$this->familia],
//                ['like','f.folio',$this->familia]]);
//                */
//            $query->andFilterWhere([
//                'f.id' => $this->familia
//            ]);
//        }
//        
//        if(!empty($this->servicio_ofrecido))
//            $query->andFilterWhere([
//                'so.id' => $this->servicio_ofrecido,
//            ]);
//        
//        if(!empty($this->division_escolar)){
//            $query->andFilterWhere([
//                'a.id_divisionescolar' => $this->division_escolar,
//            ]);
//        }
//        
//        if(!empty($this->establecimiento)){
//            $query->andFilterWhere([
//                'div.id_establecimiento' => $this->establecimiento,
//            ]);
//        }
//                
//        
//
//        $dataProvider->sort->attributes['familia'] = [           
//            'asc' => ['a.id_grupofamiliar' => SORT_ASC],
//            'desc' => ['a.id_grupofamiliar' => SORT_DESC],
//        ];
//        
//        $session->set('serviciosalumnos', $dataProviderSession->getModels());
//        
//        return $dataProvider;
//    }
}
