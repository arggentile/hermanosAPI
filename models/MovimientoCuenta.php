<?php

namespace app\models;

use Yii;
use \app\models\base\MovimientoCuenta as BaseMovimientoCuenta;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "movimiento_cuenta".
 */
class MovimientoCuenta extends BaseMovimientoCuenta
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
                # custom validation rules
            ]
        );
    }
}
