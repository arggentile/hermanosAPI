<?php

namespace app\models;

use Yii;
use \app\models\base\Establecimiento as BaseEstablecimiento;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "establecimiento".
 */
class Establecimiento extends BaseEstablecimiento
{

    public $establecimiento;
    
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
                 ['celular', 'required'],
              ['establecimiento, fecha_apertura' ,'safe'],
              ['xfecha_apertura', 'required', 'message'=>'Fecha Invalida.'],
              ['xfecha_apertura', 'date', 'format' => 'php:d-m-Y','message'=>'Fecha Invalida.'],
              ['fecha_apertura', 'rulesFechaAperturaValida'],
             ]
        );
    }
    
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'xfecha_apertura' => 'Fec.Apertura',
            ]
        );
    }
    
    
    
    /***********************************************************/
    /***********************************************************/
    public function getXfecha_apertura()
    {
        if (!empty($this->fecha_apertura) && $valor = Fecha::convertirFecha($this->fecha_apertura,"Y-m-d","d-m-Y"))
        {
            return $valor;
        } else
        {
            return $this->fecha_apertura;
        }
    }

    public function setXfecha_apertura($value)
    {
        if (!empty($value) && $valor = Fecha::convertirFecha($value,"d-m-Y","Y-m-d")) 
        {
            $this->fecha_apertura = $valor;
        } else
        {
            $this->fecha_apertura = $value;
        }
    }
 

    /***********************************************************/
    /***********************************************************/
    public static function getEstablecimientos(){
        $dropciones = Establecimiento::find()->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'nombre');
    }
    
    public function rulesFechaAperturaValida() {
        if(!empty($this->fecha_apertura)){
            if(Fecha::esFechaMayor(date('Y-m-d'), $this->fecha_apertura)){
                $this->addError('xfecha_apertura', 'La fecha de Apertura debe ser menor a la fecha Actual.');
            }    
        }
    }    
}
