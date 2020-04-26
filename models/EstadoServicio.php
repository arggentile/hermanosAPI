<?php

namespace app\models;

use Yii;
use \app\models\base\EstadoServicio as BaseEstadoServicio;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "estado_servicio".
 */
class EstadoServicio extends BaseEstadoServicio
{

    const ID_ABIERTA = 1; //'A'; //abierta libre asignar 
    const ID_ABONADA = 2; //'PA'; //abonada
    const ID_EN_DEBITOAUTOMATICO = 3; //'DA';
    const ID_ABONADA_EN_DEBITOAUTOMATICO = 4; //'PA/DA';
    const ID_EN_CONVENIOPAGO = 5; //'CP';  
    const ID_ABONADA_EN_CONVENIOPAGO = 6; //'PA/CP';
    
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
    
    public static function getEstadoServicios(){
        $dropciones = EstadoServicio::find()->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'descripcion');
    }           
}
