<?php

namespace app\models;

use Yii;
use \app\models\base\ServicioDebitoAutomatico as BaseServicioDebitoAutomatico;
use yii\helpers\ArrayHelper;

use app\models\DebitoAutomatico;
/**
 * This is the model class for table "servicio_debito_automatico".
 */
class ServicioDebitoAutomatico extends BaseServicioDebitoAutomatico
{
    public $id_alumno;

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
                  ['id_alumno','safe']
             ]
        );
    }
    
    
    
    public function getDetalleMiServicio(){
        if($this->tiposervicio== DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS){
            $servicioAlumno = \app\models\ServicioAlumno::findOne($this->id_servicio);

            $alumno = \app\models\Alumno::findOne($servicioAlumno->id_alumno);
            return $servicioAlumno->datosMiServicio;
       }else
        if($this->tiposervicio==DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO){
            $cuotaConvenioPago = \app\models\CuotaConvenioPago::findOne($this->id_servicio);
            $convenioPago = \app\models\ConvenioPago::findOne($cuotaConvenioPago->id_conveniopago); 
            $familia = \app\models\GrupoFamiliar::findOne($convenioPago->id_familia);
            return "C.P N:".$convenioPago->id. " Nº cuota: ".$cuotaConvenioPago->id." -$".$cuotaConvenioPago->monto;
        }
    }
    
    public function getDetalleAlumno(){
        if($this->tiposervicio  == DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS){
            $servicioAlumno = \app\models\ServicioAlumno::findOne($this->id_servicio);
            $alumno = \app\models\Alumno::findOne($servicioAlumno->id_alumno);
            return $alumno->persona->apellido . " ". $alumno->persona->nombre;
          }else
        if($this->tiposervicio == DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO ){
            $cuotaConvenioPago = \app\models\CuotaConvenioPago::findOne($this->id_servicio);
            $convenioPago = \app\models\ConvenioPago::findOne($cuotaConvenioPago->id_conveniopago); 
            $familia = \app\models\GrupoFamiliar::findOne($convenioPago->id_familia);
            return $familia->apellidos;
        }
    }
    
    public function getDetalleResultadoProcesamiento() {       
        $result = '';
        if(!empty($this->resultado_procesamiento)){
            $modelResultadoCBU = \app\models\ResultadoCbuPatagonia::find()->andWhere(['like', 'codigo', $this->resultado_procesamiento])->one();
            if(!empty($modelResultadoCBU))
                $result = $modelResultadoCBU->descripcion;
        }
            return $result;
    }
    
            
    
    public function getMiTiket(){
        
        if($this->correcto){
            if($this->tiposervicio == DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS)
                $tiposervicio = ServiciosTiket::ID_SERVICIOS;
            elseif($this->tiposervicio == DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO)
                $tiposervicio = ServiciosTiket::ID_CUOTA_CP;
            
                
            $serviciosTiket = ServiciosTiket::find()
                ->andWhere(['id_servicio'=>$this->id_servicio])
                ->andWhere(['tiposervicio'=> $tiposervicio])->one();
            if(!empty($serviciosTiket)){
                $modelTiket = Tiket::findOne($serviciosTiket->id_tiket);
                if(!empty($modelTiket))
                    return $modelTiket;
            }
            return null;
        }
        return null;
            
    }
     
     
}
