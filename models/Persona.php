<?php

namespace app\models;

use Yii;
use \app\models\base\Persona as BasePersona;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "persona".
 */
class Persona extends BasePersona
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
                ['fecha_nacimiento', 'date', 'format' => 'php:Y-m-d'],
                ['xfecha_nacimiento', 'date', 'format' => 'php:d-m-Y','message'=>'Ingrese una fecha valida'],
                ['fecha_nacimiento', 'rulesNacimientoValido'],
                ['nro_documento', 'rulesDocumentoValido'],
                ['xfecha_nacimiento', 'required'],
                
            ]
        );
    }
    
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                'id_tipodocumento' => 'Tipo.Doc',
                'id_sexo' => 'Sexo',
                'nro_documento' => 'Nro.Doc',
                'xfecha_nacimiento' => 'Fec.Nacimiento'
            ]
        );
    }
    
   
    /**************************************************************/
    /**************************************************************/
    public function getXfecha_nacimiento()
    {
        if (!empty($this->fecha_nacimiento) && $valor = Fecha::convertirFecha($this->fecha_nacimiento,"Y-m-d","d-m-Y"))
        {
            return $valor;
        } else
        {
            return $this->fecha_nacimiento;
        }
    }

    public function setXfecha_nacimiento($value)
    {
        if (!empty($value) && $valor = Fecha::convertirFecha($value,"d-m-Y","Y-m-d")) 
        {        
            $this->fecha_nacimiento = $valor;
        } else
        {
            $this->fecha_nacimiento = $value;
        }
    }
    
    public function getMiDomicilio(){
        $domicilio = $this->calle . " Nº " . $this->nro_calle; 
        if(!empty($this->piso))
            $domicilio.=" Piso: ".$this->piso;
        if(!empty($this->dpto))
            $domicilio.=" Dpto: ".$this->dpto;
        return $domicilio;
    }
    public function getMiTelContacto(){
       return "Tel: ".$this->telefono." Cel: ".$this->celular; 
    }
    
    
   
    
    public function rulesNacimientoValido() {
        if(!empty($this->fecha_nacimiento) && Fecha::esFechaMayor(date('Y-m-d'), $this->fecha_nacimiento)){
            $this->addError('xfecha_nacimiento', 'La fecha de Nacimiento debe ser menor a la fecha Actual.');
        }    
    }
    
    public function rulesDocumentoValido() {
        if(!empty($this->nro_documento)){
            if(!is_numeric($this->nro_documento))
                $this->addError('nro_documento','Nro documento solo adminite digitos.');
            
            if(strlen($this->nro_documento)!==11 && $this->id_tipodocumento=='4')
                $this->addError('nro_documento','El CUIL debe poseer 11 digitos');
            
            if(strlen($this->nro_documento)!==11 && $this->id_tipodocumento=='5')
                $this->addError('nro_documento','El CUIT debe poseer 11 digitos');
            
            if( (strlen($this->nro_documento)<6 || strlen($this->nro_documento)>8)  && $this->id_tipodocumento=='1')
                $this->addError('nro_documento','El DNI debe poseer 6 a 8 digitos');
        }    
    }
}
