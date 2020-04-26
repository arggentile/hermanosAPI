<?php

namespace app\models;

use Yii;
use \app\models\base\ServicioConvenioPago as BaseServicioConvenioPago;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "servicio_convenio_pago".
 */
class ServicioConvenioPago extends BaseServicioConvenioPago
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
    
    public function getDetalleServicio()
    {
        return $this->idServicio->miServiciotomado->nombre . " ('". $this->idServicio->miServiciotomado->idTiposervicio->descripcion."')";
    }
}
