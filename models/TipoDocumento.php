<?php

namespace app\models;

use Yii;
use \app\models\base\TipoDocumento as BaseTipoDocumento;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tipo_documento".
 */
class TipoDocumento extends BaseTipoDocumento
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
    
    public static function getTipoDocumentos(){
        $dropciones = TipoDocumento::find()->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'siglas');
    }     
}
