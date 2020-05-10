<?php

namespace app\models;

use Yii;
use \app\models\base\DebitoAutomaticoRegistro as BaseDebitoAutomaticoRegistro;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "debito_automatico_registro".
 */
class DebitoAutomaticoRegistro extends BaseDebitoAutomaticoRegistro
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
