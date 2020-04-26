<?php

namespace app\models;

use Yii;
use \app\models\base\BonificacionAlumno as BaseBonificacionAlumno;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "bonificacion_alumno".
 */
class BonificacionAlumno extends BaseBonificacionAlumno
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

   public function attributeLabels()
    {
        return ArrayHelper::merge(
             parent::attributeLabels(),
             [
                'id_bonificacion' => 'Bonificación',
          
             ]
        );
    }
    
    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                [['id_bonificacion'],'required','message'=>'Seleccione la bonificacion'],
             ]
        );
    }
}
