<?php

namespace app\models;

use Yii;
use \app\models\base\Alumno as BaseAlumno;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "alumno".
 */
class Alumno extends BaseAlumno
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
                ['establecimiento','safe'],
                ['hijo_profesor','required'],
                ['fecha_ingreso', 'date', 'format' => 'php:Y-m-d'],
                ['xfecha_ingreso', 'date', 'format' => 'php:d-m-Y', 'message'=>'Ingrese una fecha valida'],
                ['fecha_egreso', 'date', 'format' => 'php:Y-m-d'],
                ['xfecha_egreso', 'date', 'format' => 'php:d-m-Y', 'message'=>'Ingrese una fecha valida'],
                ['fecha_ingreso', 'rulesIngresoValido'],
                ['egresado', 'rulesEgresoValido'],
             ]
        );
    }
    
    public function attributeLabels()
    {
        return ArrayHelper::merge(
             parent::attributeLabels(),
             [
                'id_divisionescolar' => 'Division Escolar',
                'xfecha_ingreso' => 'Fec. Ingreso',
                'nro_legajo' => 'Legajo',
                'id_grupofamiliar'=>'Debe seleccionar un Grupo Familiar'
             ]
        );
    }
    
    public function getXhijo_profesor(){
        if($this->hijo_profesor=='1' || $this->hijo_profesor==1 || $this->hijo_profesor==true)
            return 1;
        else    
          if($this->hijo_profesor=='0' || $this->hijo_profesor==0 || $this->hijo_profesor==false)
            return 0;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMiGrupofamiliar()
    {
        return $this->hasOne(\app\models\GrupoFamiliar::className(), ['id' => 'id_grupofamiliar']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMiPersona()
    {
        return $this->hasOne(\app\models\Persona::className(), ['id' => 'id_persona']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMiDivisionescolar()
    {
        return $this->hasOne(\app\models\DivisionEscolar::className(), ['id' => 'id_divisionescolar']);
    }
    
    public function getSoyActivo(){
        if($this->activo=='0')
            return "NO";
        else
            return "SI";
    }   
    
    public static function getDatosUnAlumno($idAlumno){
        $alumno = Alumno::findOne($idAlumno);
        if(!empty($alumno)){
            return $alumno->miPersona->apellido . " ". $alumno->miPersona->nombre;
        }else
            return "";
    
    }


    /***********************************************************/
    /***********************************************************/
    public function getXfecha_ingreso()
    {
        if (!empty($this->fecha_ingreso) && $valor = Fecha::convertirFecha($this->fecha_ingreso,"Y-m-d","d-m-Y"))
        {
            
            return $valor;
        } else
        {
            return $this->fecha_ingreso;
        }
    }

    public function setXfecha_ingreso($value)
    {
        if (!empty($value) && $valor = Fecha::convertirFecha($value,"d-m-Y","Y-m-d"))
        {
            
            $this->fecha_ingreso = $valor;
        } else
        {
            $this->fecha_ingreso = $value;
        }
    }
    
    public function getXfecha_egreso()
    {
        if (!empty($this->fecha_egreso) && $valor = Fecha::convertirFecha($this->fecha_egreso,"Y-m-d","d-m-Y"))
        {
            
            return $valor;
        } else
        {
            return $this->fecha_egreso;
        }
    }

    public function setXfecha_egreso($value)
    {
        if (!empty($value) && $valor = Fecha::convertirFecha($value,"d-m-Y","Y-m-d"))
        {
            
            $this->fecha_egreso = $valor;
        } else
        {
            $this->fecha_egreso = $value;
        }
    }
    
    public function rulesIngresoValido() {
        if(!empty($this->fecha_ingreso)){
            if(Fecha::esFechaMayor(date('Y-m-d'), $this->fecha_ingreso)){
                $this->addError('xfecha_ingreso', 'La fecha de inscripcion debe ser menor a la fecha Actual.');
            }    
            $fechaNacimiento = ($this->miPersona && $this->miPersona->fecha_nacimiento)?$this->miPersona->fecha_nacimiento:null;
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($fechaNacimiento));
            if(Fecha::esFechaMayor( $this->fecha_ingreso, $fechaNacimiento)){
                $this->addError('xfecha_ingreso', 'La fecha de inscripcion debe ser mayor a la fecha de nacimiento.');
            }
        }
    }    
    
    public function rulesEgresoValido() {
        if($this->egresado=='1' && empty($this->fecha_egreso))
            $this->addError('xfecha_egreso', 'Fecha de egreso invalida/vacia.');
            
       
    }    
    
    
    public static function getAlumnosFamiliaDrop($idfamilia){
        //$modelAlumno = Alumno::findOne($idalumno);
     
        $dropciones = Alumno::find()->alias('a')->joinWith('persona p')
                
                ->andWhere(['id_grupofamiliar'=>$idfamilia])->asArray()->all();
    
        return ArrayHelper::map($dropciones, 'id', function ($element) {
                    return $element['persona']['apellido'] . ' '. $element['persona'] ['nombre'];
                });
    }
}
