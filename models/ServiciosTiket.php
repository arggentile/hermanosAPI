<?php

namespace app\models;

use Yii;
use \app\models\base\ServiciosTiket as BaseServiciosTiket;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "servicios_tiket".
 */
class ServiciosTiket extends BaseServiciosTiket
{

    const ID_SERVICIOS = 1;
    const ID_CUOTA_CP = 2;
    
    
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
    
    public function getMiDetalleFactura(){
        if($this->tiposervicio == self::ID_SERVICIOS){
            $modelServicioAlumno = ServicioAlumno::findOne($this->id_servicio);
            return $modelServicioAlumno->servicio->categoriaservicio->descripcion . " " .$modelServicioAlumno->servicio->nombre . " " . $this->monto_abonado; 
        }elseif($this->tiposervicio == self::ID_CUOTA_CP){
            $modelCuotaCP = CuotaConvenioPago::findOne($this->id_servicio);
            return "CP: " . $modelCuotaCP->conveniopago->nombre . " Nro Cuota: " . $modelCuotaCP->nro_cuota . " " . $this->monto_abonado; 
            
        }
    }
}
