<?php

/**
 * Description of EgresoAlumnoForm
 *
 * @author agus
 */

namespace app\models\forms;

use yii\base\Model;

class EgresoAlumnoForm extends Model
{
    public $id_establecimiento;
    public $id_divisionescolar;     //nombre de la plantilla texto a producir
    public $egreso;
    public $fecha_egreso;
    
    public $es_egreso;
    
    
    public function rules()
    {
        return [
            [['es_egreso'], 'required'],   
            [['id_establecimiento','id_divisionescolar'],'safe'],
            [['es_egreso','fecha_egreso'], 'safe'],               
            ['es_egreso', 'rulesEgresoValido'],
        ];
    }
    
    
    public function rulesEgresoValido() {
      
        if(empty($this->fecha_egreso))
            $this->addError('fecha_egreso', 'Fecha invalida/vacia.');
        
        if($this->es_egreso=='0' && (empty($this->id_establecimiento) || empty($this->id_divisionescolar)))
            $this->addError('id_divisionescolar', 'Debe seleccionar la Division Escolar a migrar los Alumnos.');
        
    }    
}
