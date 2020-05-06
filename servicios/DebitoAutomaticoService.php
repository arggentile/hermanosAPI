<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\servicios;

use Yii;

use app\models\ServicioOfrecido;
use app\models\ServicioAlumno;
use app\models\EstadoServicio;
use app\models\DebitoAutomatico;
use app\models\ServicioDebitoAutomatico;
use app\models\FormaPago;

use app\helpers\GralException;

/**
 *
 * @author agentile
 */
class DebitoAutomaticoService {

    
    
    public static function eliminarDebitoAutomatico($id){        
        try{
            $transaction = Yii::$app->db->beginTransaction(); 
            $model = DebitoAutomatico::findOne($id);
            if(empty($model))
                throw new GralException('Modelo no encontrado para su eliminación');
            
            if(!$model->getSePuedeEliminar())
                throw new GralException('No se puede eliminar el Debito, el mismo ya fue procesado.');
            
            $valid = true;
            $serviciosDebitoAutomatico = ServicioDebitoAutomatico::find()->andWhere(['id_debitoautomatico'=>$id])->all();
            if(!empty($serviciosDebitoAutomatico)){
                foreach($serviciosDebitoAutomatico as $serDA){
                    /* @var ServicioDebitoAutomatico $serDA */
                    if($serDA->tiposervicio == DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS){
                        $modelServicioAlumno = ServicioAlumno::findOne($serDA->id_servicio);
                        if(empty($modelServicioAlumno))
                            throw new GralException('No se encuentra el servicio dentro del Debito a cambiar su estado');
                        
                        $modelServicioAlumno->id_estado = EstadoServicio::ID_ABIERTA;
                        if(!$modelServicioAlumno->save()){
                            $valid = false;
                            \Yii::$app->getModule('audit')->data('errorAction', json_encode($modelServicioAlumno->errors));  
                            throw new GralException('Error al retroceder el Servicio del Alumno');
                        }
                    }elseif($serDA->tiposervicio == DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO){
                        $modelCCP = \app\models\CuotaConvenioPago::findOne($serDA->id_servicio);
                        if(empty($modelCCP)){
                           throw new GralException('No se encuentra la Cuota del Convenio de Pago a revertir');
                        }
                        
                        $modelCCP->id_estado = EstadoServicio::ID_ABIERTA;
                        if(!$modelCCP->save()){
                            $valid = false;
                            \Yii::$app->getModule('audit')->data('errorAction', json_encode($modelServicioAlumno->errors));  
                            throw new GralException('Error al retroceder la Cuota del Convenio de Pago.');
                        }
                    }
                    if(!$serDA->delete())
                        throw new GralException('No se pudo eliminar el servicio asociado al Debito.');
                }
            }
            
            if($model->delete()){
                $transaction->commit();
                $response['success'] = true;
                $response['mensaje'] = 'Eliminación correcta';
                return $response;
            }else{
                $transaction->rollBack();
                $response['success'] = false;
                $response['mensaje'] = 'Eliminación erronea';
                $response['error_models'] =   $model->errors; 
                return $response;
            }
        }catch (GralException $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', json_encode($e));  
            throw new GralException($e->getMessage());            
        }catch (\Exception $e) {      
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw new yii\web\HttpException(500, $e->getMessage());
        }              
    }
    
    
    
    public function armarDebitoAutomatico($idDA) {
        try{
            $modelDebAut = DebitoAutomatico::findOne($idDA);
            if(!$modelDebAut)
              throw new GralException('No se encontró el Debito Automatico para armar el debito.');                
            else{
                //segun tipo de archivo a generar, llamamos a sus respetivos metodos de generacion
                if($modelDebAut->tipo_archivo == DebitoAutomatico::ID_TIPODEBITO_TC){                      
                    return $this->generaArchivoPatagoniaTC($idDA);                 
                }
                if($modelDebAut->tipo_archivo == DebitoAutomatico::ID_TIPODEBITO_CBU){                      
                    return $this->generaArchivoPatagoniaCBU($idDA);                 
                }
            }    
        }catch (GralException $e) {        
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            throw new GralException($e->getMessage());            
        }catch (Exception $e){           
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw new \yii\web\HttpException(500, "Error interno al procesar la solicitud.");
        }
    }    

    /****************************************************************/
    /************ DEBITOS TC BANCO PATAGONIA ************************/
    /****************************************************************/
    public function generaArchivoPatagoniaTC($idDA){    
        ini_set('memory_limit', -1);
        set_time_limit(999);        
        try{        
            $transaction = Yii::$app->db->beginTransaction();
        
            $modelArchivo = DebitoAutomatico::findOne($idDA);
            if(!$modelArchivo)
                throw new GralException('No se encontró el Debito Automatico a armar el debito.');              
        
            $periodoIni = $modelArchivo->inicio_periodo;
            $periodoFin = $modelArchivo->fin_periodo;
            $fechaVencimiento = $modelArchivo->fecha_debito;
        
            $sqlServiciosXItems = "SELECT 
                        D.idfamilia as idfamilia, 
                        D.nro_tarjetacredito as nro_tarjetacredito, 
                        D.idservicio as idservicio, 
                        D.monto as monto, 
                        D.tiposervicio  as tiposervicio FROM (
                    (
                    SELECT 
                        alu.id_grupofamiliar as idfamilia, 
                        fam.nro_tarjetacredito as nro_tarjetacredito, 
                        sa.id as idservicio, 
                        (sa.importe_servicio - sa.importe_descuento - sa.importe_abonado) as monto, 
                        ". DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS . " as tiposervicio

                    FROM servicio_alumno sa 
                    INNER JOIN alumno as alu ON (alu.id = sa.id_alumno)
                    INNER JOIN grupo_familiar as fam ON (fam.id = alu.id_grupofamiliar)
                    INNER JOIN servicio_ofrecido so ON (so.id = sa.id_servicio)
                    WHERE (alu.activo = '1') and (fam.id_pago_asociado= ". FormaPago::ID_DEBITO_TC . ") 
                        and (sa.id_estado = ". EstadoServicio::ID_ABIERTA  .") 
                        and ((so.fecha_vencimiento >= '".$periodoIni."') and (so.fecha_vencimiento <= '".$periodoFin."'))
                    ORDER BY fam.id
                    )
                    UNION                   
                    (
                    SELECT 
                        fam.id as idfamilia, 
                        fam.nro_tarjetacredito as nro_tarjetacredito, 
                        ccp.id as idservicio, 
                        (ccp.monto) as monto, 
                        " . DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO . " as tiposervicio
                    FROM cuota_convenio_pago ccp 
                      INNER JOIN convenio_pago cp ON (cp.id = ccp.id_conveniopago)	
                      INNER JOIN grupo_familiar as fam ON (fam.id = cp.id_familia) 
                    WHERE (cp.deb_automatico = 1) and (fam.id_pago_asociado = ". FormaPago::ID_DEBITO_TC . ")
                       and (ccp.id_estado =  ". EstadoServicio::ID_ABIERTA  .")
                       and ((ccp.fecha_establecida >= '".$periodoIni."') and (ccp.fecha_establecida <= '".$periodoFin."'))

                    ) 
                ) as D 
                ORDER BY D.idfamilia";
            
            
           $sqlMontoTotalFamiliar = "SELECT DEB.idfamilia, DEB.nro_tarjetacredito, SUM(DEB.monto) as montototal FROM (" .
                   $sqlServiciosXItems . ") AS DEB GROUP BY DEB.idfamilia,DEB.nro_tarjetacredito";
           
         
       
            $connection = Yii::$app->db;        
            $command = $connection->createCommand($sqlServiciosXItems);
            
            $commandMontosTotales = $connection->createCommand($sqlMontoTotalFamiliar);
            
            
            $result = $command->queryAll();            
            
            if(count($result)==0){
                throw new GralException('No se puede armar el archivo; no existen servicos de alumnos/familiar a debitar.');      
            }
            
            $resultMontosTotales = $commandMontosTotales->queryAll();  
            
            $carp_cont = Yii::getAlias('@webroot') . "/archivos_generados/patagonia/tc/generados";
            $filename = 'debitos-'. $modelArchivo->id .'.txt';
            $filename_1 = $filename;
            $filename = $carp_cont."/".$filename;          

            $contenido="";  
            /////HEADER  - encabezado
            $encabezado="";
            $encabezado.="0DEBLIQC ";
            $encabezado.="0025255886"; 
            $encabezado.="900000    ";
            $encabezado.=date('Ymdhm');
            $encabezado.="0                                                         *";
            $encabezado.="\r\n"; 

            $contenido.=$encabezado;

            $saldo_total = 0;                   
            $cantidad = 0;
            $procesa = true;

            $nroServicioFamilia = 1;
            $nroFamiliaAnterior = 0;
            
            //por un lado armamos el archivos
            
            foreach($resultMontosTotales as $rowTotalFamilia){
                if($procesa){ 
                    $cantidad +=1;

                    $nrofamilia = $rowTotalFamilia['idfamilia'];
                    $nrotarjeta = $rowTotalFamilia['nro_tarjetacredito'];
                    $monto = $rowTotalFamilia['montototal'];
                    $saldo_total = $saldo_total + $monto;  
                    $nroServicioFamilia = 1;       
                    $contenido.= $this->devolverLinea_PATAGONIA_TC($nrofamilia, $nrotarjeta, $cantidad, $monto, $fechaVencimiento);  
                }
            }
            
            
            //creamos servicio en el debito y actualizamos servicios de alumno y cuotas a sus estados correspondientes
            foreach($result as $rowServicio){
                $nroServicioFamilia = 1;
                if($procesa){ 
                    $servicio_da = new ServicioDebitoAutomatico();
                    $servicio_da->id_debitoautomatico = $modelArchivo->id;
                    $servicio_da->id_servicio = $rowServicio['idservicio'];
                    $servicio_da->tiposervicio = $rowServicio['tiposervicio'];
                    $servicio_da->linea = 'FAMILIA '.$rowServicio['idfamilia'] .' - MATRICULA ' .$nroServicioFamilia; 
                    $servicio_da->id_familia = $rowServicio['idfamilia'];
                    $servicio_da->importe = (float) $rowServicio['monto'] ;
                    if($rowServicio['tiposervicio'] == DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS){
                        $miServicio = ServicioAlumno::findOne($rowServicio['idservicio']);
                        $miServicio->id_estado = EstadoServicio::ID_EN_DEBITOAUTOMATICO;
                        $procesa = $servicio_da->save() && $miServicio->save();    
                    }else
                    if($rowServicio['tiposervicio'] == DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO){
                        $miServicio = \app\models\CuotaConvenioPago::findOne($rowServicio['idservicio']);
                        $miServicio->id_estado = EstadoServicio::ID_EN_DEBITOAUTOMATICO;
                        $procesa = $servicio_da->save() && $miServicio->save();    
                    }                                               
                }    
            }
            
            
         
            $modelArchivo->saldo_enviado = $saldo_total;
                
            $pie="9DEBLIQC ";
            $pie.="0025255886"; 
            $pie.="900000    ";
            $pie.=date('Ymdhm');
            $pie.=str_pad($cantidad,7,"0",STR_PAD_LEFT);
            $saldo_total=number_format($saldo_total, 2);
            $saldo_total = str_replace(",","",str_replace(".","",$saldo_total));
            $pie.=str_pad($saldo_total,15,"0",STR_PAD_LEFT);
            $pie.="                                    ";
            $pie.="*";
            $pie.="\r\n";

            $contenido.=$pie;

            $modelArchivo->registros_enviados = $cantidad;                
            $modelArchivo->saldo_entrante = 0;             
            $procesa = $procesa && $modelArchivo->save();

            if($procesa){                    
                if (!$handle = fopen("$filename", "w")) { 
                    throw new GralException('Error severo; nose puede abrir o grabar el archivo en el disco.');      
                   // $se_genero = false;  
                    //return false;
                    //exit;
                }else {
                    ftruncate($handle,filesize("$filename"));
                }

                $link = "";
                if (fwrite($handle, $contenido) === FALSE){
                    throw new GralException('Error severo; nose puede abrir o grabar el archivo en el disco.');      
                    //$se_genero = false;
                    //return false;
                    //exit;
                }else{ 
                    fclose($handle);	 
                    $se_genero = true; 
                    $archivo  = $modelArchivo->id;
                }

                $transaction->commit();
                //colocar codigo para avisar por correo
                $response['success'] = true;
                return $response;           
            } 
        }catch (GralException $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            throw new GralException($e->getMessage());            
        }catch (Exception $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));   
            throw new \yii\web\HttpException(500, $e->getMessage());
        }           
    } //actionGenArc_Patagonia_TC       
    
    private function devolverLinea_PATAGONIA_TC($nrofamilia, $nrotarjeta, $cantidad, $monto, $fecha_vencimiento_pago){
        try{
            $contenido='';
            $contenido.="1";
            $nrotarjeta = str_replace("","0",$nrotarjeta);
            $contenido.=str_pad($nrotarjeta,16," ",STR_PAD_LEFT);                
            $contenido.="   ";
            $contenido.=str_pad($nrofamilia,8,"0",STR_PAD_LEFT); 
            $fechavencimiento = $fecha_vencimiento_pago;
            $fechavencimiento = str_replace("-","",$fechavencimiento);            
            $contenido.=str_replace("-","",$fechavencimiento); //formato de la fecha yyyymmdd
            $contenido.="0005";    
            $montoCuota = number_format($monto, 2);
            $montoCuota = str_replace(",","",str_replace(".","",$montoCuota));            
            $contenido.=str_pad($montoCuota,15,"0",STR_PAD_LEFT); 
            $identificador = 'F99999F'.  str_pad($nrofamilia,8,"0",STR_PAD_LEFT);
            $contenido.=   $identificador;       
            $contenido.="E"; 
            $contenido.="  ";
            $contenido.="                          ";
            $contenido.="*";
            $contenido.="\r\n";
            return $contenido;
        }catch (\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));   
            throw new \yii\web\HttpException(500, $e->getMessage());
        }   
    }  
    
    /****************************************************************/
    /************ DEBITOS CBU BANCO PATAGONIA ***********************/
    /****************************************************************/    
    
    public function generaArchivoPatagoniaCBU($idDA){          
        ini_set('memory_limit', -1);
        set_time_limit(240);
        set_time_limit(-1);        
        try{
            $transaction = Yii::$app->db->beginTransaction();
        
            $modelArchivo = DebitoAutomatico::findOne($idDA);
            if(!$modelArchivo)
                throw new GralException('No se encontró el Debito Automatico armar el debito.');  
        
            $periodoIni = $modelArchivo->inicio_periodo;
            $periodoFin = $modelArchivo->fin_periodo;
            $fechaVencimiento = $modelArchivo->fecha_debito;
        
            $fechaVencimientoLinea = \app\helpers\Fecha::formatear($modelArchivo->fecha_debito, "Y-m-d","d-m-Y");
                
            $sqlServiciosXItems = "SELECT 
                        D.idfamilia as idfamilia, 
                        D.cbu as cbu, 
                        D.idservicio as idservicio, 
                        D.monto as monto, 
                        D.tiposervicio  as tiposervicio FROM (
                    (
                    SELECT 
                        alu.id_grupofamiliar as idfamilia, 
                        fam.cbu_cuenta as cbu, 
                        sa.id as idservicio, 
                        (sa.importe_servicio - sa.importe_descuento - sa.importe_abonado) as monto, 
                        ". DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS . " as tiposervicio

                    FROM servicio_alumno sa 
                    INNER JOIN alumno as alu ON (alu.id = sa.id_alumno)
                    INNER JOIN grupo_familiar as fam ON (fam.id = alu.id_grupofamiliar)
                    INNER JOIN servicio_ofrecido so ON (so.id = sa.id_servicio)
                    WHERE (alu.activo = '1') and (fam.id_pago_asociado= ". FormaPago::ID_DEBITO_CBU . ") 
                        and (sa.id_estado = ". EstadoServicio::ID_ABIERTA  .") 
                        and ((so.fecha_vencimiento >= '".$periodoIni."') and (so.fecha_vencimiento <= '".$periodoFin."'))
                    ORDER BY fam.id
                    )
                    UNION                   
                    (
                    SELECT 
                        fam.id as idfamilia, 
                        fam.cbu_cuenta as cbu, 
                        ccp.id as idservicio, 
                        (ccp.monto) as monto, 
                        " . DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO . " as tiposervicio
                    FROM cuota_convenio_pago ccp 
                      INNER JOIN convenio_pago cp ON (cp.id = ccp.id_conveniopago)	
                      INNER JOIN grupo_familiar as fam ON (fam.id = cp.id_familia) 
                    WHERE (cp.deb_automatico = 1) and (fam.id_pago_asociado = ". FormaPago::ID_DEBITO_CBU . ")
                       and (ccp.id_estado =  ". EstadoServicio::ID_ABIERTA  .")
                       and ((ccp.fecha_establecida >= '".$periodoIni."') and (ccp.fecha_establecida <= '".$periodoFin."'))

                    ) 
                ) as D 
                ORDER BY D.idfamilia";
            
            
           $sqlMontoTotalFamiliar = "SELECT DEB.idfamilia, DEB.cbu, SUM(DEB.monto) as montototal FROM (" .
                   $sqlServiciosXItems . ") AS DEB GROUP BY DEB.idfamilia,DEB.cbu";
           
         
       
            $connection = Yii::$app->db;        
            $command = $connection->createCommand($sqlServiciosXItems);
            
            $commandMontosTotales = $connection->createCommand($sqlMontoTotalFamiliar);
            
            
            $result = $command->queryAll();            
            
            if(count($result)==0){
                throw new GralException('No se puede armar el archivo; no existen servicos de alumnos/familiar a debitar.');      
            }
            
            $resultMontosTotales = $commandMontosTotales->queryAll();  
            
            $carp_cont = Yii::getAlias('@webroot') . "/archivos_generados/patagonia/cbu/generados";
            $filename = 'debitos-'. $modelArchivo->id .'.txt';                
            $filename = $carp_cont."/".$filename;          

            $contenido="";

            /////HEADER  - encabezado
            $encabezado="";
            $encabezado.="H30630291727";
            $encabezado.="CUOTA     ";
            $encabezado.='618';
            $encabezado.= str_replace("/","",date('d/m/Y'));
            $encabezado.='            ';
            $encabezado.='COLEGIO VECCHI                     ';
            $encabezado.=str_pad('',120," ",STR_PAD_LEFT);
            $encabezado.="\r\n"; 

            $contenido.=$encabezado;

            $saldo_total = 0;                   
            $cantidad = 0;
            $procesa = true;

            $nroServicioFamilia = 1;
            $nroFamiliaAnterior = 0;
            
            //por un lado armamos el archivos
            
            foreach($resultMontosTotales as $rowTotalFamilia){
                if($procesa){ 
                    $cantidad +=1;

                    $nrofamilia = $rowTotalFamilia['idfamilia'];
                    $cbu = $rowTotalFamilia['cbu'];
                    $monto = $rowTotalFamilia['montototal'];
                    $saldo_total = $saldo_total + $monto;  
                    $nroServicioFamilia = 1;                   
                    $contenido.= $this->devolverLinea_PATAGONIA_CBU($nrofamilia, $cbu, $cantidad, $monto, $fechaVencimientoLinea, $nroServicioFamilia); 
                }
            }
            
            
            //creamos servicio en el debito y actualizamos servicios de alumno y cuotas a sus estados correspondientes
            foreach($result as $rowServicio){
                $nroServicioFamilia = 1;
                if($procesa){ 
                    $servicio_da = new ServicioDebitoAutomatico();
                    $servicio_da->id_debitoautomatico = $modelArchivo->id;
                    $servicio_da->id_servicio = $rowServicio['idservicio'];
                    $servicio_da->tiposervicio = $rowServicio['tiposervicio'];
                    $servicio_da->linea = 'FAMILIA '.$rowServicio['idfamilia'] .' - MATRICULA ' .$nroServicioFamilia; 
                    $servicio_da->id_familia = $rowServicio['idfamilia'];
                    $servicio_da->importe = (float) $rowServicio['monto'] ;
                    if($rowServicio['tiposervicio'] == DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS){
                        $miServicio = ServicioAlumno::findOne($rowServicio['idservicio']);
                        $miServicio->id_estado = EstadoServicio::ID_EN_DEBITOAUTOMATICO;
                        if(!$servicio_da->save()){
                            $procesa = false;
                            \Yii::$app->getModule('audit')->data('errorServicioDebitoAutomatico', \yii\helpers\VarDumper::dumpAsString($servicio_da->errors)); 
                        }
                        if(!$miServicio->save()){
                            $procesa = false;
                            \Yii::$app->getModule('audit')->data('errorServicioAlumno', \yii\helpers\VarDumper::dumpAsString($miServicio->errors)); 
                        }
                        
                    }else
                    if($rowServicio['tiposervicio'] == DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO){
                        $miServicio = \app\models\CuotaConvenioPago::findOne($rowServicio['idservicio']);
                        $miServicio->id_estado = EstadoServicio::ID_EN_DEBITOAUTOMATICO;
                        if(!$servicio_da->save()){
                            $procesa = false;
                            \Yii::$app->getModule('audit')->data('errorServicioDebitoAutomatico', \yii\helpers\VarDumper::dumpAsString($servicio_da->errors)); 
                        }
                        if(!$miServicio->save()){
                            $procesa = false;
                            \Yii::$app->getModule('audit')->data('errorServicioCupta', \yii\helpers\VarDumper::dumpAsString($miServicio->errors)); 
                        }
                    }                                               
                }    
            }
            
           

            $modelArchivo->saldo_enviado = $saldo_total;

            $pie="T";
            $pie.=str_pad($cantidad,7,"0",STR_PAD_LEFT); 
            $saldo_total=number_format($saldo_total, 2);
            $saldo_total = str_replace(",","",str_replace(".","",$saldo_total));
            $pie.=str_pad($saldo_total,15,"0",STR_PAD_LEFT);
            $pie.=str_pad("",177," ",STR_PAD_LEFT);
            $pie.="\r\n";
            $contenido.=$pie;

            $modelArchivo->registros_enviados = $cantidad;                
            $modelArchivo->saldo_entrante = 0;                
            $procesa = $procesa && $modelArchivo->save();                

            if($procesa){                    
                if (!$handle = fopen("$filename", "w")) { 
                    throw new GralException('Error severo; nose puede abrir o grabar el archivo en el disco.'); 
                    //$se_genero = false;  
                    //return false;
                    //exit;
                }else {
                    ftruncate($handle,filesize("$filename"));
                }

                $link = "";
                if (fwrite($handle, $contenido) === FALSE){
                    throw new GralException('Error severo; nose puede abrir o grabar el archivo en el disco.'); 
                    //$se_genero = false;
                    //return false;
                    //exit;
                }else{ 
                    fclose($handle);	 
                    $se_genero = true; 
                    $archivo  = $modelArchivo->id;
                }                    

                $transaction->commit();
                //colocar codigo para avisar por correo
                $response['success'] = true;
                return $response;                 
            }else
               throw new GralException("Error al generar el archivo");         //fin del procesa == TRUE            
        }catch (GralException $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            throw new GralException($e->getMessage());            
        }catch (\Exception $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));   
            throw new \yii\web\HttpException(500, $e->getMessage());
        }                 
    } //actionGenArc_Patagonia_TC         
    
    private function devolverLinea_PATAGONIA_CBU($nrofamilia, $cbu, $cantidad, $monto, $fecha_vencimiento_pago, $nroServicioFamilia){
        try{
            $contenido='';
            $contenido.="D";
            $contenido.='00000000000';
            $contenido.=str_pad($cbu,22," ",STR_PAD_RIGHT);

            $identificadorFamilia   = "FG1".str_pad($nrofamilia,5,"0",STR_PAD_LEFT);        
            $contenido.= str_pad($identificadorFamilia,22," ",STR_PAD_RIGHT);

            $fechavencimiento = $fecha_vencimiento_pago;
            $fechavencimiento = str_replace("-","",$fechavencimiento);            
            $contenido.=str_replace("-","",$fechavencimiento); 

            $contenido.="CUOTA     ";
            $contenido.=str_pad("",15," ",STR_PAD_RIGHT);

            $servicio='MATRICULA'.$nroServicioFamilia;

            $contenido.=str_pad($servicio,15,"M",STR_PAD_LEFT);

            $montoCuota = number_format($monto, 2);
            $montoCuota = str_replace(",","",str_replace(".","",$montoCuota));            
            $contenido.=str_pad($montoCuota,10,"0",STR_PAD_LEFT); 
            $contenido.='P';
            $contenido.='                                                                          30630291727';
            $contenido.="\r\n";
            return $contenido;
        }catch (\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));   
            throw new \yii\web\HttpException(500, $e->getMessage());
        }   
    }  
    
    

}
