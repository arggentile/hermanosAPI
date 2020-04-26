<?php

namespace app\models;

use Yii;
use \app\models\base\TipoSexo as BaseTipoSexo;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tipo_sexo".
 */
class TipoSexo extends BaseTipoSexo
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
    
        public static function getTipoSexos(){
        $dropciones = TipoSexo::find()->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'nombre');
    }  

}
