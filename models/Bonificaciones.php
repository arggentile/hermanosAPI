<?php

namespace app\models;

use Yii;
use \app\models\base\Bonificaciones as BaseBonificaciones;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "bonificaciones".
 */
class Bonificaciones extends BaseBonificaciones
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
                ['valor','rulesValorValido'],
                ['cantidad_hermanos','rulesUnicaActiva']
            ]
        );
    }
    
    public function rulesValorValido() {
        if($this->valor > 100)
            $this->addError('valor', 'El valor de la Bonificacion debe ser menor o igualal 100 %.');
            
    }
    
    public function rulesUnicaActiva() {
        $modelExiste = Bonificaciones::find()
                ->andWhere(['activa'=>'1'])->andWhere(['automatica'=>'1'])
                ->andWhere(['cantidad_hermanos' => $this->cantidad_hermanos])->all();
        
        if($this->isNewRecord &&  ($this->activa == '1' && $this->automatica == '1' && count($modelExiste)>0))
            $this->addError('valor', 'Ya existe una bonificación activa para la cantidad de hermanos ingresada');
            
    }
    
    /**************************************/
    public static function getDetalleBonificacionesActivasAlumnoDrop(){
        $dropciones = Bonificaciones::find()->andWhere(['activa' => '1'])->andWhere(['automatica' => '0'])->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 
                function ($element) {
                    return $element['descripcion'] . '  (Monto: '. $element['valor'].'%)';
                });
        
    }  
}
