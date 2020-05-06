<?php

namespace app\models;

use Yii;
use \app\models\base\FormaPago as BaseFormaPago;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "forma_pago".
 */
class FormaPago extends BaseFormaPago
{

    const ID_ESFECTIVO = 1;
    const ID_POSNET_TC = 2;
    const ID_POSTNET_TD = 3;    
    const ID_DEBITO_CBU = 4;
    const ID_DEBITO_TC = 5;
    const ID_DEPOSITO_BANCARIO = 6;
    
    
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
    
    public static function getFormasPago(){
        $dropciones = FormaPago::find()->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'nombre');
    }    
    
    public static function getFormasPagoHabilitadosCobrosCaja(){
        $idsHablitados = [FormaPago::ID_ESFECTIVO,FormaPago::ID_POSNET_TC, FormaPago::ID_POSTNET_TD];
        $dropciones = FormaPago::find()->andWhere(['in','id',$idsHablitados])->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'nombre');
    }   
}
