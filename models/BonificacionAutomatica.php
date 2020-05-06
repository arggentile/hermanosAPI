<?php

namespace app\models;

use Yii;
use \app\models\base\BonificacionAutomatica as BaseBonificacionAutomatica;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "bonificacion_automatica".
 */
class BonificacionAutomatica extends BaseBonificacionAutomatica
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
                ['cantidad_hemanos','rulesUnicaActiva']
            ]
        );
    }
    
    public function rulesValorValido() {
        if($this->valor > 100)
         $this->addError('valor', 'El valor de la Bonificacion debe ser menor o igualal 100 %.');
            
    }
    
    public function rulesUnicaActiva() {
        $modelExiste = BonificacionAutomatica::find()
                ->andWhere(['activa'=>'1'])
                ->andWhere(['cantidad_hemanos' => $this->cantidad_hemanos])->all();
        
        if($this->isNewRecord &&  ($this->activa == '1' && count($modelExiste)>0))
            $this->addError('valor', 'Ya existe una bonificación activa para la cantidad de hermanos ingresada');
            
    }
}
