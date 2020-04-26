<?php

namespace app\models;

use Yii;
use \app\models\base\Factura as BaseFactura;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

use app\helpers\GralException;

use app\servicios\afip\FacturaAfipService;

/**
 * This is the model class for table "factura".
 */
class Factura extends BaseFactura
{
    public $ptovta;

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
                  ['ptovta','safe']
             ]
        );
    }
    
    
    public static function GeneraFactura($ptovta, $tipodoc, $nrodoc, $monto, $tiket) {
        ini_set('default_socket_timeout', 600);
        try{
            $facturaAfip = new FacturaAfipService(1);
       
        
            $facturaAfip->tipoDoc = 'DNI';
            $facturaAfip->nroDoc = str_replace("-", "", (str_replace(".", "", $nrodoc)));
            $facturaAfip->monto = $monto;        
            $hayerrores = false;


            if ($facturaAfip->conerror === FALSE) {
                    
                $facturaAfip->generaFactura();

                if ($facturaAfip->nroCae > 0){
                    $fechaVencimientoCae = $facturaAfip->fechaVtoCae;

                    $fechaVencimientoCae = substr($fechaVencimientoCae, 6, 2) . "-" . substr($fechaVencimientoCae, 4, 2) . "-" . substr($fechaVencimientoCae, 0, 4);
                    $fechaVencimientoCae = \app\helpers\Fecha::formatear($fechaVencimientoCae,'d-m-Y','Y-m-d');

                    $modelFactura = new Factura();
                    $modelFactura->ptovta = (string) $ptovta;
                    $modelFactura->fecha_factura = date('20170324');
                    $modelFactura->informada = '1';
                    $modelFactura->fecha_informada = date('20170324');
                    $modelFactura->monto = $facturaAfip->monto;
                    $modelFactura->cae = $facturaAfip->nroCae;
                    $modelFactura->nroFactura = (string) $facturaAfip->nroFactura;
                    $modelFactura->id_tiket = $tiket;

                    if ($modelFactura->save()) {

                        $hayerrores = false;
                    } else {
                        $hayerrores = true;
                    }
                } else {
                    $hayerrores = true;
                    /*var_dump($facturaAfip);
                    exit;*/
                }
            } else {
                var_dump($facturaAfip);
                exit;
                $hayerrores = true;
            }
        
            /*var_dump($facturaAfip->nroCae);
            exit;*/
            if ($hayerrores === FALSE) {
                return $modelFactura;
            } else {
               /* $logs = new LogsFacturas;
                $logs->fecha_proceso = date('Y-m-d');
                $logs->id_tiket = $tiket;
                $logs->nro_factura = '';
                $logs->informacion = $facturaAfip->error;
                $logs->save();*/
                return null;
            }
        }catch (GralException $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            //Yii::$app->session->setFlash('error',Yii::$app->params['operacionFallida']);  
            throw new GralException($e->getMessage());
        }catch (\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw new HttpException(500, $e->getMessage());
            //Yii::$app->session->setFlash('error',Yii::$app->params['operacionFallida']);             
        }   
    }

    /*     * ************************************************************ */

    public function getMiNroFactura() {
        if (!empty($this->id_tiket)) {
            return "000" . $this->ptovta . "-" . str_pad($this->nroFactura, 8, "0", STR_PAD_LEFT);
        } else
            return "";
    }

    public function getCantServiciosPagos() {
        if (!empty($this->id_tiket)) {
            $cantServicios = ServiciosAbogado::model()->findAll('id_tiket=' . $this->id_tiket);
            if (!empty($cantServicios)) {
                return "<span class='label label-warning'>" . count($cantServicios) . "</span>";
            } else {

                return "<span class='label label-warning'>1</span>";
            }
        } else
            return "";
    }
    
    public function getMiTiket() {
        return $this->hasOne(\app\models\Tiket::className(), ['id' => 'id_tiket']);        
    }
    
    public function getMiCliente() {
        $modelTiket = Tiket::findOne($this->id_tiket);
        $modelAbogado = Abogado::findOne($modelTiket->id_cliente);
        
        return $modelAbogado->getMisDatos();
        
    }
    
}
