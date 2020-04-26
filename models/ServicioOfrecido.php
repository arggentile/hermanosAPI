<?php

namespace app\models;

use Yii;
use \app\models\base\ServicioOfrecido as BaseServicioOfrecido;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "servicio_ofrecido".
 */
class ServicioOfrecido extends BaseServicioOfrecido
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
                ['devengamiento_automatico','required'], 
                ['xfecha_inicio', 'date', 'format' => 'php:d-m-Y','message'=>'Ingrese una fecha Valida'],
                ['xfecha_fin', 'date', 'format' => 'php:d-m-Y','message'=>'Ingrese una fecha Valida'],
                ['xfecha_vencimiento', 'date', 'format' => 'php:d-m-Y','message'=>'Ingrese una fecha Valida'],                 
                ['xfecha_fin','rulesControlFechas'],  
                [['fecha_inicio'],'rulesControlFechas','skipOnEmpty' => false],
                [['devengamiento_automatico'], 'integer'],
                 ['activo','required']
             ]
        );
    }
    
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'xfecha_inicio' => 'Inicio Servicio',
                'xfecha_fin' => 'Fin Servicio',
                'xfecha_vencimiento' => 'Fec. Vencimiento',
                'id_categoriaservicio' => 'Categoria',  
                
            ]
        );
    }
    
    public function getXdevengamiento_automatico(){
        if($this->devengamiento_automatico=='1' || $this->devengamiento_automatico==1 || $this->devengamiento_automatico==true)
            return 1;
        else    
          if($this->devengamiento_automatico=='0' || $this->devengamiento_automatico==0 || $this->devengamiento_automatico==false)
            return 0;
    }  


    /***********************************************************/
    /***********************************************************/
    /*
     * Retorna un conjunto de servicios ofrecidos
     */
    
    public static function getServicios(){
        $dropciones = ServicioOfrecido::find()->asArray()->all();
        return $dropciones;
    }

    public static function getServiciosDrop(){
        $dropciones = ServicioOfrecido::find()->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'nombre');
    }
    
    public static function getServiciosConDetalle(){
        $dropciones = ServicioOfrecido::find()->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 
                function ($element) {
                    return $element['nombre'] . '  (Monto: $'. $element['importe'].')';
                });
    }
    
    public static function getServiciosActivosConDetalle(){
        $dropciones = ServicioOfrecido::find()->andWhere(['activo'=>true])->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 
                function ($element) {
                    return $element['nombre'] . '  (Monto: $'. $element['importe'].')';
                });
    }
    
    public function getDetallePeriodo(){
         return $this->xfecha_inicio . "  al  " . $this->xfecha_fin; 
    }

    public function getDetalleServicio(){
        return $this->categoriaservicio->descripcion . " - " . $this->nombre;
    }    
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    
    public function getMiTiposervicio()
    {
        return $this->hasOne(CategoriaServicioOfrecido::className(), ['id' => 'id_categoriaservicio']);
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */    
    public function getCantDivisionesAsociadas()
    {
        $divisiones = ServicioDivisionEscolar::find()->where('id_servicio='.$this->id)->count();        
        return $divisiones;
        
    }
  

    /***********************************************************/
    /******************* ATRIBUTOS VIRTUALES PARA FECHAS *******/
    public function getXfecha_inicio()
    {
        if (!empty($this->fecha_inicio) && $valor = Fecha::convertirFecha($this->fecha_inicio,"Y-m-d","d-m-Y"))
        {
            return $valor;
        } else
        {
            return $this->fecha_inicio;
        }
    }

    public function setXfecha_inicio($value)
    {
        if (!empty($value) && $valor = Fecha::convertirFecha($value,"d-m-Y","Y-m-d"))
        {
            $this->fecha_inicio = $valor;
        } else
        {
            $this->fecha_inicio = $value;
        }
    }
    

    public function getXfecha_fin()
    {
        if (!empty($this->fecha_fin) && $valor = Fecha::convertirFecha($this->fecha_fin,"Y-m-d","d-m-Y"))
        {
            return $valor;
        } else
        {
            return $this->fecha_fin;
        }
    }

    public function setXfecha_fin($value)
    {
        if (!empty($value) && $valor = Fecha::convertirFecha($value,"d-m-Y","Y-m-d"))
        {
            $this->fecha_fin = $valor;
        } else
        {
            $this->fecha_fin = $value;
        }
    }

    public function getXfecha_vencimiento()
    {
        if (!empty($this->fecha_vencimiento) && $valor = Fecha::convertirFecha($this->fecha_vencimiento,"Y-m-d","d-m-Y"))
        {
            return $valor;
        } else
        {
            return $this->fecha_vencimiento;
        }
    }

    public function setXfecha_vencimiento($value)
    {
        if (!empty($value) && $valor = Fecha::convertirFecha($value,"d-m-Y","Y-m-d"))
        {
            $this->fecha_vencimiento = $valor;
        } else
        {
            $this->fecha_vencimiento = $value;
        }
    } 
    
    /***************************************************/
    /*************************** RULES *****************/
    public function rulesControlFechas($attribute, $params){       
        if ($this->devengamiento_automatico=='1' && empty($this->fecha_vencimiento))
            $this->addError('xfecha_vencimiento', 'Ingrese la fecha de vencimiento del Servicio.');
        
        if ($this->devengamiento_automatico=='1' && empty($this->fecha_inicio))
            $this->addError('xfecha_inicio', 'Ingrese la fecha de inicio del Periodo.');
        
        if ($this->devengamiento_automatico=='1' && empty($this->fecha_fin))
            $this->addError('xfecha_fin', 'Ingrese la fecha de inicio del Periodo.');
        
       if( (!empty($this->fecha_fin) && !empty($this->fecha_inicio)) && Fecha::esFechaMayor($this->fecha_fin, $this->fecha_inicio)){
        $this->addError('xfecha_inicio', 'La fecha de Inicio del Periodo debe ser menor igual a la fecha Final.');
       } 
    }   // fin rulesControlFechas
}
