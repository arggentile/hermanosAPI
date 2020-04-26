<?php

namespace app\models;

use Yii;
use \app\models\base\BonificacionServicioAlumno as BaseBonificacionServicioAlumno;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "bonificacion_servicio_alumno".
 */
class BonificacionServicioAlumno extends BaseBonificacionServicioAlumno
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
