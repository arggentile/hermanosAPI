<?php

namespace app\models;

use Yii;
use \app\models\base\Cuentas as BaseCuentas;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cuentas".
 */
class Cuentas extends BaseCuentas
{

    const IDtipo_moviento_egreso = 2;
    const IDtipo_moviento_ingreso = 1;
    
    const DETALLEtipo_moviento_egreso = 'EGRESO';
    const DETALLEtipo_moviento_ingreso = 'INGRESO';
    
    const ID_CAJA_COLEGIO = 1;
    const ID_CAJA_PATAGONIA = 2;
    
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
    
    public static function getDropMisCuentas(){        
        $dropciones = Cuentas::find()->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'nombre'); 
    } 
    
    
}
