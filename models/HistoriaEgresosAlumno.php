<?php

namespace app\models;

use Yii;
use \app\models\base\HistoriaEgresosAlumno as BaseHistoriaEgresosAlumno;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "historia_egresos_alumno".
 */
class HistoriaEgresosAlumno extends BaseHistoriaEgresosAlumno
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
