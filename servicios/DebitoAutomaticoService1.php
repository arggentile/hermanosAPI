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
        set_time_limit(480);        
        try{        
            $transaction = Yii::$app->db->beginTransaction();
        
            $modelArchivo = DebitoAutomatico::findOne($idDA);
            if(!$modelArchivo)
                throw new GralException('No se encontró el Debito Automatico a armar el debito.');              
        
            $periodoIni = $modelArchivo->inicio_periodo;
            $periodoFin = $modelArchivo->fin_periodo;
            $fechaVencimiento = $modelArchivo->fecha_debito;
        
       
            $sql = 
                "SELECT * FROM (
                   (
                    SELECT a.nrofamilia as nrofamilia, a.nrotarjeta, sa.id as idservicio, 
                            (sa.importe_servicio - sa.importe_descuento - sa.importe_abonado) as monto, 
                            ". DebitoAutomatico::ID_SERVICIO_CUOTAS . " as tiposervicio
                        FROM servicio_alumno sa 
                        INNER JOIN ( 
                            SELECT a.id as idalumno, f.id as nrofamilia, f.nro_tarjetacredito as nrotarjeta 
                             FROM alumno a INNER JOIN grupo_familiar f ON (f.id = a.id_grupofamiliar) 
                              WHERE (f.id_pago_asociado = ". FormaPago::ID_DEBITO_TC . ") 
                        ) a ON (a.idalumno = sa.id_alumno) 
                        
                        INNER JOIN servicio_ofrecido so ON (so.id = sa.id_servicio)                     
                        INNER JOIN categoria_servicio_ofrecido cts ON (cts.id = so.id_categoriaservicio) 
                    WHERE (sa.id_estado = ". EstadoServicio::ID_ABIERTA  .") 
                        and ((so.fecha_vencimiento >= '".$periodoIni."') and (so.fecha_vencimiento <= '".$periodoFin."'))
                    ORDER BY a.nrofamilia
                   )
                   UNION
                   (
                    SELECT a.nrofamilia as nrofamilia, a.nrotarjeta, ccp.id as idservicio, 
                            (sa.importe_servicio - sa.importe_descuento - sa.importe_abonado) as monto, 
                            ". DebitoAutomatico::ID_SERVICIO_CONVENIO_PAGO . " as tiposervicio
                        FROM servicio_alumno sa 
                        INNER JOIN ( 
                            SELECT a.id as idalumno, f.id as nrofamilia, f.nro_tarjetacredito as nrotarjeta 
                             FROM alumno a INNER JOIN grupo_familiar f ON (f.id = a.id_grupofamiliar) 
                              WHERE (f.id_pago_asociado = ". FormaPago::ID_DEBITO_TC . ") 
                        ) a ON (a.idalumno = sa.id_alumno) 
                        INNER JOIN convenio_pago cp ON cp.id_familia = a.nrofamilia
                        INNER JOIN cuota_convenio_pago ccp ON (ccp.	id_conveniopago  = cp.id)                     
                     WHERE (ccp.id_estado = ". EstadoServicio::ID_ABIERTA  .") 
                     and ((ccp.fecha_establecida >= '".$periodoIni."') and (ccp.fecha_establecida <= '".$periodoFin."'))
                    ) 
                ) as D";

            $connection = $connection = Yii::$app->db;        
            $command = $connection->createCommand($sql);
            
            \Yii::$app->getModule('audit')->data('errorAction', $command->getRawSql());   
            $result = $command->queryAll(); 
            if(empty($result)){
                throw new GralException('No se puede armar el archivo; no existen servicos de alumnos/familiar a debitar.');      
            }            
            
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
                
            //variables que mantiene la cantidad y totales de servicios de cada 
            //familia en cada linea, para cada familia solo se manda un regln; no se puede detallar cada servicio por separado
            $nroServicioFamilia = 1;
            $nroFamiliaAnterior = $result[0]['nrofamilia'];
            $nrotarjetaAnterior = $result[0]['nrotarjeta'];
            $totalMontoFamilia=0;
                
            foreach($result as $row){                  
                if($procesa == TRUE){ 
                    $cantidad +=1;

                    $nrofamilia = $row['nrofamilia'];
                    $nrotarjeta = $row['nrotarjeta'];
                    $monto = $row['monto'];
                    $saldo_total = $saldo_total + $monto;  

                    if($nroFamiliaAnterior!=$nrofamilia){
                        $contenido.= $this->devolverLinea_PATAGONIA_TC($nroFamiliaAnterior, $nrotarjeta, $cantidad, $totalMontoFamilia, $fechaVencimiento);  
                        $nroServicioFamilia = 1;
                        $nroFamiliaAnterior = $nrofamilia;
                        $totalMontoFamilia=$monto;
                    }else
                        $totalMontoFamilia +=  $monto;


                    $servicio_da = new ServicioDebitoAutomatico();
                    $servicio_da->id_debitoautomatico = $modelArchivo->id;
                    $servicio_da->id_servicio = $row['idservicio'];
                    $servicio_da->tiposervicio = $row['tiposervicio'];
                    $servicio_da->linea = 'FAMILIA '.$nrofamilia .' - MATRICULA ' .$nroServicioFamilia;
                    $servicio_da->id_familia = $nrofamilia;
                    $servicio_da->importe = (float) $monto ;
                    if($row['tiposervicio']== DebitoAutomatico::ID_SERVICIO_CUOTAS){
                        $miServicio = ServicioAlumno::findOne($row['idservicio']);
                        $miServicio->id_estado = EstadoServicio::ID_EN_DEBITOAUTOMATICO;
                        $procesa = $servicio_da->save() && $miServicio->save();    
                    }else
                    if($row['tiposervicio'] == DebitoAutomatico::ID_SERVICIO_CONVENIO_PAGO){
                        $miServicio = \app\models\CuotaConvenioPago::findOne($row['idservicio']);
                        $miServicio->id_estado = EstadoServicio::ID_EN_DEBITOAUTOMATICO;
                        $procesa = $servicio_da->save() && $miServicio->save();    
                    } 
                }                    

            }
            $contenido.= $this->devolverLinea_PATAGONIA_TC($nrofamilia, $nrotarjeta, $cantidad, $totalMontoFamilia, $fechaVencimiento);                           

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
                    FROM servicio_alumno as sa 
                      INNER JOIN alumno as alu ON (alu.id = sa.id_alumno)
                      INNER JOIN grupo_familiar as fam ON (fam.id = alu.id_grupofamiliar)
                      INNER JOIN convenio_pago as cp ON (cp.id_familia = fam.id)
                      INNER JOIN cuota_convenio_pago ccp ON (ccp.	id_conveniopago  = cp.id)   
                    WHERE (alu.activo = '1') and (fam.id_pago_asociado = ". FormaPago::ID_DEBITO_CBU . ")
                       and (ccp.id_estado =  ". EstadoServicio::ID_ABIERTA  .")
                       and ((ccp.fecha_establecida >= '".$periodoIni."') and (ccp.fecha_establecida <= '".$periodoFin."'))

                    ) 
                ) as D 
                ORDER BY D.idfamilia";
            
            
           $sqlMontoTotalFamiliar = "SELECT DEB.idfamilia, DEB.cbu, SUM(DEB.monto) as montototal FROM (" .
                   $sqlServiciosXItems . ") AS DEB GROUP BY DEB.idfamilia,DEB.cbu";
           
         var_dump($sqlMontoTotalFamiliar);
         exit;
       
            $connection = Yii::$app->db;        
            $command = $connection->createCommand($sqlServiciosXItems);
            
            $commandMontosTotales = $connection->createCommand($sqlMontoTotalFamiliar);
            \Yii::$app->getModule('audit')->data('errorAction', $command->getRawSql());
             \Yii::$app->getModule('audit')->data('errorAction', $commandMontosTotales->getRawSql());
            
            $result = $command->queryAll();            
            
            if(empty($result)){
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
            
            foreach($resultMontosTotales as $familia){
                if($procesa){ 
                    $cantidad +=1;

                    $nrofamilia = $row['idfamilia'];
                    $cbu = $row['cbu'];
                    $monto = $row['montototal'];
                    $saldo_total = $saldo_total + $monto;  

                    if($nrofamilia!=$nroFamiliaAnterior){
                        $nroServicioFamilia = 1;
                        $nroFamiliaAnterior=$nrofamilia;    
                    }else{
                       $nroServicioFamilia+=1;                            
                    }

                    $contenido.= $this->devolverLinea_PATAGONIA_CBU($nrofamilia, $cbu, $cantidad, $monto, $fechaVencimientoLinea, $nroServicioFamilia); 

                    $servicio_da = new ServicioDebitoAutomatico();
                    $servicio_da->id_debitoautomatico = $modelArchivo->id;
                    $servicio_da->id_servicio = $row['idservicio'];
                    $servicio_da->tiposervicio = $row['tiposervicio'];
                    $servicio_da->linea = 'FAMILIA '.$nrofamilia .' - MATRICULA ' .$nroServicioFamilia; 
                    $servicio_da->id_familia = $nrofamilia;
                    $servicio_da->importe = (float) $monto ;
                    if($row['tiposervicio'] == DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS){
                        $miServicio = ServicioAlumno::findOne($row['idservicio']);
                        $miServicio->id_estado = EstadoServicio::ID_EN_DEBITOAUTOMATICO;
                        $procesa = $servicio_da->save() && $miServicio->save();    
                    }else
                    if($row['tiposervicio'] == DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO){
                        $miServicio = \app\models\CuotaConvenioPago::findOne($row['idservicio']);
                        $miServicio->id_estado = EstadoServicio::ID_EN_DEBITOAUTOMATICO;
                        $procesa = $servicio_da->save() && $miServicio->save();    
                    }                                               
                }    
            }
            
            foreach($result as $row){                  
                if($procesa == TRUE){ 
                    $cantidad +=1;

                    $nrofamilia = $row['nrofamilia'];
                    $cbu = $row['cbu'];
                    $monto = $row['monto'];
                    $saldo_total = $saldo_total + $monto;  

                    if($nrofamilia!=$nroFamiliaAnterior){
                        $nroServicioFamilia = 1;
                        $nroFamiliaAnterior=$nrofamilia;    
                    }else{
                       $nroServicioFamilia+=1;                            
                    }

                    $contenido.= $this->devolverLinea_PATAGONIA_CBU($nrofamilia, $cbu, $cantidad, $monto, $fechaVencimientoLinea, $nroServicioFamilia); 

                    $servicio_da = new ServicioDebitoAutomatico();
                    $servicio_da->id_debitoautomatico = $modelArchivo->id;
                    $servicio_da->id_servicio = $row['idservicio'];
                    $servicio_da->tiposervicio = $row['tiposervicio'];
                    $servicio_da->linea = 'FAMILIA '.$nrofamilia .' - MATRICULA ' .$nroServicioFamilia; 
                    $servicio_da->id_familia = $nrofamilia;
                    $servicio_da->importe = (float) $monto ;
                    if($row['tiposervicio'] == DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS){
                        $miServicio = ServicioAlumno::findOne($row['idservicio']);
                        $miServicio->id_estado = EstadoServicio::ID_EN_DEBITOAUTOMATICO;
                        $procesa = $servicio_da->save() && $miServicio->save();    
                    }else
                    if($row['tiposervicio'] == DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO){
                        $miServicio = \app\models\CuotaConvenioPago::findOne($row['idservicio']);
                        $miServicio->id_estado = EstadoServicio::ID_EN_DEBITOAUTOMATICO;
                        $procesa = $servicio_da->save() && $miServicio->save();    
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
            } //fin del procesa == TRUE            
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


//
//
//"SELECT * FROM (
//                    (
//                      SELECT 
//                            alu.id_grupofamiliar as idfamilia, 
//                            fam.cbu_cuenta as cbu, 
//                            sa.id as idservicio, 
//                            (sa.importe_servicio - sa.importe_descuento - sa.importe_abonado) as monto, 
//                            ". DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS . " as tiposervicio
//                            B.importeadebitar as importeadebitar,
//                            B.importeadebitar  as cantidadservicios
//                        FROM servicio_alumno sa 
//                        INNER JOIN alumno as alu ON (alu.id = sa.id_alumno)
//                        INNER JOIN grupo_familiar as fam ON (fam.id = alu.id_grupofamiliar)
//                        INNER JOIN servicio_ofrecido so ON (so.id = sa.id_servicio)                     
//                        INNER JOIN categoria_servicio_ofrecido cts ON (cts.id = so.id_categoriaservicio)
//                        
//                        INNER JOIN (                            
//                            SELECT
//                                gf.id AS idfamilia,
//                                COUNT(sa.id) as cantidadservicios,
//                                SUM(sa.importe_servicio - sa.importe_descuento) AS importeadebitar
//                            FROM
//                                servicio_alumno sa
//                             INNER JOIN alumno al ON (al.id = sa.id_alumno)
//                             INNER JOIN grupo_familiar gf ON (gf.id = al.id_grupofamiliar)
//                             INNER JOIN servicio_ofrecido so ON (so.id = sa.id_servicio)
//                             INNER JOIN categoria_servicio_ofrecido cts ON (cts.id = so.id_categoriaservicio)
//                               WHERE (al.activo ='1') and (gf.id_pago_asociado =  ". FormaPago::ID_DEBITO_CBU . ") and (sa.id_estado = ". EstadoServicio::ID_ABIERTA  .") and 
//                                    ((so.fecha_vencimiento >= '".$periodoIni."') and (so.fecha_vencimiento <= '".$periodoFin."'))
//                             GROUP BY gf.id
//                        ) as B ON (B.idfamilia = alu.id_grupofamiliar)
//                        WHERE (alu.activo = '1') and (fam.id_pago_asociado= ". FormaPago::ID_DEBITO_CBU . ") 
//                        and                         
//                       	(sa.id_estado = ". EstadoServicio::ID_ABIERTA  .") 
//                        and ((so.fecha_vencimiento >= '".$periodoIni."') and (so.fecha_vencimiento <= '".$periodoFin."'))
//                    ORDER BY fam.id
//                    
//                   )
//                   UNION
//                   (
//                    SELECT 
//                        fam.id as idfamilia, 
//                        fam.id_pago_asociado as cbu, 
//                        ccp.id as idservicio, 
//                        (ccp.monto) as monto, 
//                        " . DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO . " as tiposervicio
//                        FROM servicio_alumno as sa 
//                          INNER JOIN alumno as alu ON (alu.id = sa.id_alumno)
//                          INNER JOIN grupo_familiar as fam ON (fam.id = alu.id_grupofamiliar)
//                          INNER JOIN convenio_pago as cp ON (cp.id_familia = fam.id)
//                          INNER JOIN cuota_convenio_pago ccp ON (ccp.	id_conveniopago  = cp.id)   
//                        WHERE (alu.activo = '1') and (fam.id_pago_asociado = ". FormaPago::ID_DEBITO_CBU . ")
//                        and (ccp.id_estado = 1)
//                    
//
//
//                        SELECT 
//                        a.nrofamilia as nrofamilia, a.nrotarjeta, ccp.id as idservicio, 
//                            (sa.importe_servicio - sa.importe_descuento - sa.importe_abonado) as monto, 
//                            ". DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO . " as tiposervicio
//                        FROM servicio_alumno sa 
//              
//                        INNER JOIN ( 
//                            SELECT a.id as idalumno, f.id as nrofamilia, f.nro_tarjetacredito as nrotarjeta 
//                             FROM alumno a INNER JOIN grupo_familiar f ON (f.id = a.id_grupofamiliar) 
//                              WHERE (f.id_pago_asociado = ". FormaPago::ID_DEBITO_CBU . ") 
//                        ) a ON (a.idalumno = sa.id_alumno) 
//                        
//
//                        INNER JOIN convenio_pago cp ON cp.id_familia = a.nrofamilia
//                        INNER JOIN cuota_convenio_pago ccp ON (ccp.	id_conveniopago  = cp.id)                     
//                     WHERE (ccp.id_estado = ". EstadoServicio::ID_ABIERTA  .") 
//                     and ((ccp.fecha_establecida >= '".$periodoIni."') and (ccp.fecha_establecida <= '".$periodoFin."'))
//                    ) 
//                ) as D";