<?php

namespace app\models;

use Yii;
use \app\models\base\ServicioAlumno as BaseServicioAlumno;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "servicio_alumno".
 */
class ServicioAlumno extends BaseServicioAlumno
{

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
                # custom validation rules
            ]
        );
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMiAlumno()
    {
        return $this->hasOne(\app\models\Alumno::className(), ['id' => 'id_alumno']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMiServicio()
    {
  
        return $this->hasOne(\app\models\ServicioOfrecido::className(), ['id' => 'id_servicio']);
    }
    
    public function getDatosMiAlumno(){
        return $this->miAlumno->miPersona->nro_documento . " - " .$this->miAlumno->miPersona->apellido ." ".$this->miAlumno->miPersona->nombre;
    }    
    /*
    public function getMiFactura() {
        $modelServiciosFactura = ServiciosTiket::find()->andWhere(['id_servicio'=>$this->id])->andWhere(['tiposervicio'=>])->one();
        
        return $this->miAlumno->miPersona->nro_documento . " - " .$this->miAlumno->miPersona->apellido ." ".$this->miAlumno->miPersona->nombre;
    }   
    */
    
    public function getDatosMiServicio(){
        return "(". $this->servicio->categoriaservicio->descripcion. ") ". $this->servicio->nombre;
    }
    
    
    public function getImporteAbonar(){
        $importe=$this->importe_servicio - $this->importe_descuento - $this->importe_abonado;
        return $importe;
    }
    
    public function getImporteRestante(){
        $importe=$this->importe_servicio - $this->importe_descuento - $this->importe_abonado;
        return $importe;
    }
    
    public function getMiFactura(){
        $modelServicioTiket = ServiciosTiket::find()->andWhere(['tiposervicio'=> ServiciosTiket::ID_SERVICIOS])->andWhere(['id_servicio'=>$this->id])->one();
        if(!empty($modelServicioTiket)){
            $modelTiket = Factura::find()->andWhere(['id_tiket'=>$modelServicioTiket->id_tiket])->one();
            if(!$modelTiket)
                return false;
            else {
                return $modelTiket;
            }
        }else
            return false;
    }
    
    public function getDetalleEstado(){
        switch ($this->id_estado){
            case EstadoServicio::ID_ABIERTA:
                 $estado='<span class="text text-sa-abierto"> Adeuda</span>'; break;
            case EstadoServicio::ID_ABONADA:
                $estado='<span class="text text-sa-abonado"> Abonada</span>'; break;
            case EstadoServicio::ID_EN_CONVENIOPAGO: 
                $estado='<span class="text text-warning">Convenio Pago</span>'; break;
            
            case EstadoServicio::ID_EN_DEBITOAUTOMATICO: 
                $estado='<span class="text text-warning">Débito Automático</span>'; break;
            case EstadoServicio::ID_ABONADA_EN_DEBITOAUTOMATICO:
                    $estado='<span class="text text-sa-abonado">Abonada Deb.Automático</span>'; break;
            case EstadoServicio::ID_ABONADA_EN_CONVENIOPAGO:
                $estado='<span class="text text-sa-abonado">Abonada Convenio Pago</span>'; break;
            case EstadoServicio::ID_DESCONTADA:
                $estado='<span class="text text-sa-abonado">SIN/PAGOS </span>'; break;
        }
        return $estado;
        
    }
    
    public function getDetalleEstadoExcel(){
        switch ($this->id_estado){
            case EstadoServicio::ID_ABIERTA:
                $estado='Adeuda'; break;
            case EstadoServicio::ID_ABONADA: 
                $estado='Liquidado'; break;
            case EstadoServicio::ID_EN_CONVENIOPAGO: 
                $estado='En convenio pago'; break;
            
            case EstadoServicio::ID_EN_DEBITOAUTOMATICO:  
                $estado='Enviada Debito Automatico'; break;
            case EstadoServicio::ID_ABONADA_EN_DEBITOAUTOMATICO: 
                $estado='Abonada por Debito Automatico'; break;
            case EstadoServicio::ID_ABONADA_EN_CONVENIOPAGO:
                $estado='Abonada en Convenio de Pago'; break;
            case EstadoServicio::ID_DESCONTADA:
                $estado='<span class="text text-sa-abonado">SIN/PAGOS </span>'; break;
        }
        return $estado;
        
    }
    
    public static function getDetalleDatos($idServicio){
        $servicio = self::findOne($idServicio);
        return "(". $servicio->servicio->categoriaservicio->descripcion. ") ". $servicio->servicio->nombre;    
        
    }
}
