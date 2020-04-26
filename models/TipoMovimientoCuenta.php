<?php

namespace app\models;

use Yii;
use \app\models\base\TipoMovimientoCuenta as BaseTipoMovimientoCuenta;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tipo_movimiento_cuenta".
 */
class TipoMovimientoCuenta extends BaseTipoMovimientoCuenta
{

    
    const IDtipo_moviento_egreso = 2;
    const IDtipo_moviento_ingreso = 1;
    
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
    
    public static function getTipoMovimientoCuenta(){
        $dropciones = MovimientoCuenta::find()->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'descripcion');
    }       
}
