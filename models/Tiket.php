<?php

namespace app\models;

use Yii;
use \app\models\base\Tiket as BaseTiket;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tiket".
 */
class Tiket extends BaseTiket
{
   
    public $cantidad_servicios;
    public $importe_servicios;
    
    public $monto_afavor;
    public $importe_abonado;
    public $pagototal;
    
    public $errorimportes;
    
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'bedezign\yii2\audit\AuditTrailBehavior'
            ]
        );
    }   
    
    public function attributeLabels()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
              'id_cuentapagadora' => 'Cuenta/Caja',
              'id_tipopago' => 'Medio Pago',
              'xfecha_tiket' => 'Fecha Tiket',              
             ]
        );
    }
    
    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [                
                [['fecha_tiket'], 'date', 'format' => 'php:Y-m-d'],                               
                [['xfecha_tiket'], 'date', 'format' => 'php:d-m-Y', 'message'=>'Ingrese una Fecha Valida'],                              
                [['xfecha_tiket','detalles','monto_afavor','importe_abonado'],'required'],
                [['fecha_tiket'],'rulesFechaPagoHabil'],
                [['pagototal','errorimportes','importe_servicios','cantidad_servicios'],'safe'],
                
                [['fecha_tiket'],'rulesFechaPagoHabilitado'],
                [['importe'],'rulesControlImportesAbonado'], 
             ]
        );
    }
    
    
    public function getXfecha_tiket()
    {
        if (!empty($this->fecha_tiket) && $valor = Fecha::convertirFecha($this->fecha_tiket,"Y-m-d","d-m-Y"))
        {
            return $valor;
        } else {
            return $this->fecha_tiket;
        }
    }

    public function setXfecha_tiket($value)
    {
        if (!empty($value) && $valor = Fecha::convertirFecha($value,"d-m-Y","Y-m-d"))
        {
            $this->fecha_tiket = $valor;            
        }else{
            $this->fecha_tiket = $value;            
        }
    }
    
    public function rulesFechaPagoHabil() {
        if(Fecha::esFechaMayor(date('Y-m-d'), $this->fecha_tiket)){
            $this->addError('xfecha_tiket', 'La fecha del Tiket debe ser menor a la fecha Actual.');
        }  
       
    }
    
    public function rulesFechaPagoHabilitado() {
        if(Fecha::esFechaMayor(date('Y-m-d'), $this->fecha_tiket)){
            $this->addError('xfecha_tiket', 'La fecha del Tiket debe ser menor a la fecha Actual.');
        }  
       
    }    
    
    
    public function rulesControlImportesAbonado() {
        if (($this->cantidadservicios==0)){
            $this->addError('errorimportes', 'Seleccione al menos un servicio a abonar.');                
        }
        if (($this->cantidadservicios>1) && ( ($this->importe_abonado + $this->monto_afavor) < $this->importe)){
            $this->addError('errorimportes', 'El pago parcial solo está permitido para un unico Servicio.');                
        }
        
        if ((($this->importe_abonado + $this->monto_afavor) > $this->importe) && ($this->monto_afavor>0)){
            $this->addError('errorimportes', 'La suma del importe abonado y el saldo a favor no debe superar el iomporte del tiket. Al utilizar monto a favor del abogado; la suma de los importes abonados debe er igual omenor');                
        }
        
        /*
         * el saldo a favor del abogado a descontar nunca pueda ser mayor que el importe del tiket
         */
        if ($this->monto_afavor > $this->importe){
            $this->addError('monto_afavor', 'El monto del Saldo a favor nunca puede superar el importe del Tiket.');                
        }
    }
}
