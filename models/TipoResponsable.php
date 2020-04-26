<?php

namespace app\models;

use Yii;
use \app\models\base\TipoResponsable as BaseTipoResponsable;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tipo_responsable".
 */
class TipoResponsable extends BaseTipoResponsable
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
    
    public static function getTipoResponsables(){
        $dropciones = TipoResponsable::find()->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'nombre');
    }
}
