<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\servicios;

use Yii;
use yii\web\HttpException;

use app\models\GrupoFamiliar;
use app\models\Persona;
use app\models\Alumno;
use app\models\EstadoServicio;
use app\models\FormaPago;
use app\models\ConvenioPago;
use app\models\CuotaConvenioPago;
use app\models\ServicioConvenioPago;
use app\models\ServicioOfrecido;
use app\models\ServicioAlumno;
use app\models\Tiket;
use app\models\ServiciosTiket;

use app\helpers\GralException;

/**
 *
 * @author agentile
 */
class CajaServices {

    private static function resolverCuentaCobradora($tipoPago){
        if($tipoPago== FormaPago::ID_ESFECTIVO)
            return \app\models\Cuentas::ID_CAJA_COLEGIO;
        else
        if($tipoPago == FormaPago::ID_POSNET_TC || $tipoPago == FormaPago::ID_POSTNET_TD)
            return \app\models\Cuentas::ID_CAJA_PATAGONIA;
        
        throw new GralException('Forma de Pago no aceptada para el cobro por Caja');
    }
    
    public static function generarTiket(Tiket $dataTiket, $idsServiciosAlumno = null, $idCuotasCP = null, $generarFactura=true){
        $transactionTiket = Yii::$app->db->beginTransaction();
        try{
            $modelTiket = new Tiket();          
            $modelTiket->setAttributes($dataTiket->attributes);
            $modelTiket->fecha_pago = $modelTiket->fecha_tiket;
            $modelTiket->id_cuentapagadora = self::resolverCuentaCobradora($modelTiket->id_tipopago);
            
            $valid = false;  
            $generoFactura = false;
            $generoTiket = false;
            
            $avisoAfip = false;
            $errorsFactura = null;
            $errorsTiket = null;
            
            if($modelTiket->save() && 
                self::acentarMovimientosCaja($modelTiket->id_cuentapagadora, \app\models\TipoMovimientoCuenta::IDtipo_moviento_ingreso, $modelTiket->importe, 'Ingresos', $modelTiket->fecha_pago,'',$modelTiket->id_tipopago, $modelTiket->id)){
                $generoTiket = true;
                $valid = true;
                
                if(!empty($idsServiciosAlumno)){
                    foreach($idsServiciosAlumno as $idModelAlumno){
                        $modelServicioAlumno = ServicioAlumno::findOne($idModelAlumno);
                        if(!$modelServicioAlumno)
                            throw new GralException ('Error al asociar el servicio del alumno, al tiket');
                        
                        $modelServicioTiket = new ServiciosTiket();
                        $modelServicioTiket->monto_abonado = $modelServicioAlumno->getImporteRestante();
                        $modelServicioTiket->id_tiket = $modelTiket->id;
                        $modelServicioTiket->id_servicio = $modelServicioAlumno->id;
                        $modelServicioTiket->tiposervicio= ServiciosTiket::ID_SERVICIOS;
                        
                        $modelServicioAlumno->id_estado = EstadoServicio::ID_ABONADA;
                        $modelServicioAlumno->importe_abonado = $modelServicioAlumno->getImporteRestante();
                        if(!$modelServicioTiket->save()){
                            $valid = false;
                            \Yii::$app->getModule('audit')->data('errorModeloServcioTiket', \yii\helpers\VarDumper::dumpAsString($modelServicioAlumno->errors));  
                        
                        }
                        if(!$modelServicioAlumno->save()){
                            $valid = false;
                            \Yii::$app->getModule('audit')->data('errorModeloServcioAlumno', \yii\helpers\VarDumper::dumpAsString($modelServicioAlumno->errors));  
                        }
                    }
                }
                if(!empty($idCuotasCP)){
                    foreach($idCuotasCP as $idModelCuotaCP){
                        $modelCuotaCP = CuotaConvenioPago::findOne($idModelCuotaCP);
                        if(!$modelCuotaCP)
                            throw new GralException ('Error al asociar el servicio de cuotas , al tiket');
                        
                        $modelServicioTiket = new ServiciosTiket();
                        $modelServicioTiket->monto_abonado = $modelCuotaCP->getImporteRestante();
                        $modelServicioTiket->id_tiket = $modelTiket->id;
                        $modelServicioTiket->id_servicio = $modelCuotaCP->id;
                        $modelServicioTiket->tiposervicio= ServiciosTiket::ID_CUOTA_CP;                        
                        
                        $modelCuotaCP->id_estado = EstadoServicio::ID_ABONADA;
                        $modelCuotaCP->importe_abonado = $modelCuotaCP->monto;
                        
                        if(!$modelServicioTiket->save()){
                            $valid = false;
                            \Yii::$app->getModule('audit')->data('errorModeloServcioTiket', \yii\helpers\VarDumper::dumpAsString($modelServicioAlumno->errors));  
                        }
                        
                        if(!$modelCuotaCP->save()){
                            $valid = false;
                            \Yii::$app->getModule('audit')->data('errorModelCuotaCP', \yii\helpers\VarDumper::dumpAsString($modelCuotaCP->errors));  
                        }
                    }
                }
                
                if(!$valid){
                    $transactionTiket->rollBack();
                }else{
                    $transactionTiket->commit();
                    if($generarFactura) {
                        $ptoVta = Yii::$app->params['ptoVtaAfip'];
                        $resultFactura = \app\models\Factura::generaFactura($ptoVta, $modelTiket->importe, $modelTiket->fecha_tiket, $modelTiket->id);
                        if($resultFactura['success']){
                            $generoFactura = true;
                            $idFactura = $resultFactura['modelFactura']->id;
                            $resultAvisoFactura = \app\models\Factura::avisarAfip($idFactura, $ptoVta, "CUIL", $modelTiket->dni_cliente, $modelTiket->importe, $modelTiket->fecha_tiket, $modelTiket->id);
                            if($resultAvisoFactura['success'])
                                $avisoAfip = true;                        
                        }                    
                    }    
                }
            }else{
                \Yii::$app->getModule('audit')->data('errorModeloTiket', \yii\helpers\VarDumper::dumpAsString($modelTiket->errors));  
                $transactionTiket->rollBack();
                $errorsTiket = $modelTiket->errors; 
            }
            
            $response['generoTiket'] = $generoFactura;
            $response['generoFactura'] = $generoFactura;
            $response['avisoAfip'] = $avisoAfip;
            
            if($valid){               
                $response['success'] = true;
                $response['mensaje'] = 'Carga correcta';   
                $response['modelsTiket'] = $modelTiket;               
            }else{
                $response['success'] = false;
                $response['error_modelTiket'] =   $errorsTiket; 
                $response['error_modelFactura'] =   $errorsFactura; 
            }
            return $response;
        }catch (GralException $e) {   
            (isset($transactionTiket) && $transactionTiket->isActive)?$transactionTiket->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            throw new GralException($e->getMessage());            
        }catch (\Exception $e){           
            (isset($transactionTiket) && $transactionTiket->isActive)?$transactionTiket->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw new \yii\web\HttpException(500, $e->getMessage());
        }  
    } 
    
    
    
    
    public static function acentarMovimientosCaja($idcuenta, $tipomovimiento, $importe, $detalleMovimiento, $fechaoperacion, $comentario = '', $id_tipopago, $id_servicio) {
        $transactionMovimiento = Yii::$app->db->beginTransaction();
        try {
            $modelCuenta = \app\models\Cuentas::findOne((int)$idcuenta); 
            if(!$modelCuenta)
                throw new GralException('No existe la cuenta a registrar el movimiento');
            
            $modelMovimientos = new \app\models\MovimientoCuenta();
                
            if ($tipomovimiento == \app\models\TipoMovimientoCuenta::IDtipo_moviento_ingreso) {
                $modelCuenta->saldo_actual += $importe;
            } elseif ($tipomovimiento == \app\models\TipoMovimientoCuenta::IDtipo_moviento_egreso) {
                $modelCuenta->saldo_actual -= $importe;
            }

            $modelMovimientos->id_cuenta = $modelCuenta->id;
            $modelMovimientos->id_tipo_movimiento = $tipomovimiento;
            $modelMovimientos->detalle_movimiento = $detalleMovimiento;
            $modelMovimientos->importe = $importe;                
            $modelMovimientos->fecha_realizacion =$fechaoperacion;               
            $modelMovimientos->comentario = $comentario;                

            $modelMovimientos->id_tipopago = $id_tipopago;                

            $modelMovimientos->id_hijo = $id_servicio;
                
            if ($modelCuenta->save() && $modelMovimientos->save()){
                $transactionMovimiento->commit();
                return $modelMovimientos->id;
            }else{        
                $error = $modelMovimientos->getErrors();
                \Yii::$app->getModule('audit')->data('errorMovimiento', \yii\helpers\VarDumper::dumpAsString($modelMovimientos->errors));  
                \Yii::$app->getModule('audit')->data('error modelCuenta', \yii\helpers\VarDumper::dumpAsString($modelCuenta->errors));  
                return false;
            }            
        }catch (GralException $e) {   
            (isset($transactionMovimiento) && $transactionMovimiento->isActive)?$transactionMovimiento->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            throw new GralException($e->getMessage());            
        }catch (\Exception $e){           
            (isset($transactionMovimiento) && $transactionMovimiento->isActive)?$transactionMovimiento->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw new \yii\web\HttpException(500, $e->getMessage());
        }  
    }
}
