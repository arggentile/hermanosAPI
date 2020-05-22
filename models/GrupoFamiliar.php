<?php

namespace app\models;

use Yii;
use \app\models\base\GrupoFamiliar as BaseGrupoFamiliar;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "grupo_familiar".
 */
class GrupoFamiliar extends BaseGrupoFamiliar
{

    public $responsable;

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bedezign\yii2\audit\AuditTrailBehavior'
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
               ['folio','integer'],
               ['folio','unique','message'=>'El Nº de Folio esta asignada a otra familia'],                
               [['id_pago_asociado'] , 'rulesControlTipoPago'], 
               ['responsable','safe'],
            ]
        );
    }
    
    public function attributeLabels()
    {
        return ArrayHelper::merge(
             parent::attributeLabels(),
             [
                'id_pago_asociado' => 'Pago Asociado',
                'nro_tarjetacredito' => 'Nº Tarjeta Credito',
             ]
        );
    }  
    public function fields()
    {
        $fields = parent::fields();
        
        $fields["responsablePrincipal"] = function($model){
            return $model->miResponsableCabecera;
        };

        return $fields;        
    }
    
    /*
    static public function getDatosUnaFamilia($idFamilia){
        $familia = GrupoFamiliar::findOne($idFamilia);
        if(!empty($familia)){
            return $familia->apellidos;
        }else
            return "";
    
    }*/
    
    public function getAlumnosActivos()
    {
        return $this->hasMany(\app\models\Alumno::className(), ['id_grupofamiliar' => 'id'])->where(['activo'=>true]);
    }
    
    /************************************************************************/
    /************************************************************************/    
    public function rulesControlTipoPago($attribute, $params) {        
        if (($this->id_pago_asociado == '4') || ($this->id_pago_asociado == 4)) {
            $patron = "/^[[:digit:]]{22}$/";
            if (!preg_match($patron, $this->cbu_cuenta)) {                
                $this->addError('cbu_cuenta', 'Nro de CBU Invalido.El mismo debe poseer un formato de 22 dígitos seguidos.');
            }
            $patronAfip = "/^[[:digit:]]{11}$/";
            if(empty($this->cuil_afip_pago) ||  (!preg_match($patronAfip, $this->cuil_afip_pago))) {                
                $this->addError('cuil_afip_pago', 'Nro de CUIL Invalido.El mismo debe poseer un formato de 11 dígitos seguidos.');
            }
        }

        if (($this->id_pago_asociado == '5') || ($this->id_pago_asociado == 5)) {
            $patron = "/^[[:digit:]]{16}$/";
            if (!preg_match($patron, $this->nro_tarjetacredito)) {                
                $this->addError('nro_tarjetacredito', 'Nro de TC INVALIDO. El mismo debe posser un formato de 16 dígitos; seguidos!!!');
            }
            $patronAfip = "/^[[:digit:]]{11}$/";
            if(empty($this->cuil_afip_pago) || (!preg_match($patronAfip, $this->cuil_afip_pago))) {                
                $this->addError('cuil_afip_pago', 'Nro de CUIL Invalido.El mismo debe poseer un formato de 11 dígitos seguidos.');
            }
        }
    }
    
    /************************************************************************/
    /************************************************************************/
    
    public function getMiResponsableCabecera(){
        if(!empty($this->id)){
            $query = Responsable::find();
            $query->joinWith(['persona p']);
        
            $query->andFilterWhere([            
                'id_grupofamiliar' => $this->id,            
            ]);
            $query->andFilterWhere(['like', 'p.apellido', $this->responsable]);
            $query->andFilterWhere(['like', 'p.nombre', $this->responsable]);        
            $query->andFilterWhere(['like', 'p.nro_documento', $this->responsable]);

            $responsable = $query->one();
            
            if($responsable !== null)
                return $responsable->persona->apellido . " " . $responsable->persona->nombre;
               
            else
                return "";
        }else
            return "";        
    }
    
    public function getMisResponsablesCabecera(){
            $query = Responsable::find();
            $query->joinWith(['persona p']);
        
            $query->andFilterWhere(['id_grupofamiliar' => $this->id]);
            $query->andFilterWhere(['cabecera' => 1]);
            
            $query->andFilterWhere(['like', 'p.apellido', $this->responsable]);
            $query->andFilterWhere(['like', 'p.nombre', $this->responsable]);        
            $query->andFilterWhere(['like', 'p.nro_documento', $this->responsable]);

            $responsables = $query->all();
            
            $return = '';
            
            if(count($responsables) >0){
                foreach($responsables as $responsable)
                    $return .= $responsable->persona->apellido . " " . $responsable->persona->nombre ."<br />";
            }
            return $return;
    }
    
    /*
    public function getResponsableD(){
        if(!empty($this->id)){
            $query = Responsable::find();
            $query->joinWith(['idPersona p']);
        
            $query->andFilterWhere([            
                'id_grupofamiliar' => $this->id,            
            ]);
            $query->andFilterWhere(['like', 'p.apellido', $this->responsable]);
            $query->andFilterWhere(['like', 'p.nombre', $this->responsable]);        
            $query->andFilterWhere(['like', 'p.nro_documento', $this->responsable]);

            $responsable = $query->one();

           
            return $responsable;
        }else
            return "";
        
    }*/
    
    
    public function getCantidadHijos(){
        return count(Alumno::find()->where('id_grupofamiliar='.$this->id)->all());
    }
    
    public function getCantidadHijosActivos(){
        return count(Alumno::find()->where('activo=1 and id_grupofamiliar='.$this->id)->all());
    }

    public function getDatosMisHijos($activo=true){
        $detalle='';
        if($activo)
            $misHijos = $this->alumnosActivos;
        else
            $misHijos = $this->alumnos;
            
        if(!empty($misHijos)){
            $i=0;
            foreach($misHijos as $hijo){
                $i+=1;
                $detalle.= " - $i: ".$hijo->miPersona->apellido .";".$hijo->miPersona->nombre;
            }
                
        }
        return $detalle;
    }
    /*
    public function getDetalleNombreMisHijos(){
        $detalle='';
        $misHijos = $this->alumnosActivos;
        
        if(!empty($misHijos)){
            $i=0;
            foreach($misHijos as $hijo){
                $i+=1;
                $detalle.= "$i: ". $hijo->miPersona->nombre."; \n";
            }
                
        }
        return $detalle;
    }*/
    /*
    public static function getServiciosDeMisAlumnos($idFamilia){
        try{            
            $searchModel = new search\ServicioAlumnoSearch();
            $searchModel->familia = $idFamilia;
            $dataProvider = $searchModel->search(Yii::$app->request->post()); 
            return $dataProvider;
        }catch (\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            throw new \yii\web\HttpException(500, 'Error al buscar los servicos de la familia.'); 
            
        }   
        
    }*/
    
}
