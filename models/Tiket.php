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
   
    public $importe_servicios;    
    public $dni_cliente;
    
    
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
              'dni_clientes'=>'Dni Cliente'
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
                [['xfecha_tiket','detalles'],'required'],
                [['importe_servicios','dni_cliente'],'safe'],
                ['dni_cliente','required'],
                [['dni_cliente'],'rulesDniValido'],
                
                [['fecha_tiket'],'rulesFechaPagoHabilitado'],
                //[['importe'],'rulesControlImportesAbonado'], 
             ]
        );
    }
     
    public function attributes() {
        return ArrayHelper::merge(
             parent::attributes(),
             [
             'dni_cliente'
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
    
    
    public function rulesFechaPagoHabilitado() {
        if(Fecha::esFechaMayor(date('Y-m-d'), $this->fecha_tiket)){
            $this->addError('xfecha_tiket', 'Fecha invalida. Fecha futura.');
        }  
       
    }    
    
    public function rulesDniValido() {
        if(!empty($this->dni_cliente)){
            if(!is_numeric($this->dni_cliente))
                $this->addError('dni_cliente','Invalido solo adminite digitos.');
            
            if(strlen($this->dni_cliente)!==11)
                $this->addError('dni_cliente','El CUIL debe poseer 11 digitos');
            
           
        }    
    }
   
      /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupoFamiliar()
    {
        return $this->hasOne(\app\models\GrupoFamiliar::className(), ['id' => 'id_cliente']);
    }

}
