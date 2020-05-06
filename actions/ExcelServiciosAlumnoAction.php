<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\actions;

use Yii;
use yii\base\Action;

use app\models\ServicioOfrecido;
use app\models\search\ServicioOfrecidoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

use app\models\ServicioAlumno;
use app\models\search\ServicioAlumnoSearch;
use app\models\Alumno;
use app\models\search\AlumnoSearch;


class ExcelServiciosAlumnoAction extends Action
{
    public function run()
    {       
        ini_set('memory_limit', '-1');
        ini_set('set_time_limite', '900');
        ini_set('max_execution_time', 900);   
        try{
            
            
            $searchModelSerAlumnos = new \app\models\search\ServicioAlumnoSearch();            
            $dataProviderSerAlumnos = $searchModelSerAlumnos->search(Yii::$app->request->get());          
            $dataProviderSerAlumnos->setPagination(false);
                                   
            $data = $dataProviderSerAlumnos->getModels();
            
            $i = 0;                        
            $contador = count($data);

            
                $objPHPExcel = new Spreadsheet();  
               
                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
             
                
                $this->cellColor($objPHPExcel, 'A1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'B1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'C1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'D1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'E1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'F1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'G1', 'F28A8C');
                
                
                $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Alumno');
                $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Importe Servicio');
                $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Importe Descuento');
                $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Importe Abonado');
                $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Importe Abonar');
                
                $letracolumnainicio = 'A';
                $letrafilainicio = 3;
                foreach($data as $modelServicioAlumno)  {
                    $letrafilainicio1 = (string) $letrafilainicio;
                    $columnaA = 'A' . $letrafilainicio1;
                    $columnaB = 'B' . $letrafilainicio1;
                    $columnaC = 'C' . $letrafilainicio1;
                    $columnaD = 'D' . $letrafilainicio1;
                    $columnaE = 'E' . $letrafilainicio1;
                    $columnaF = 'F' . $letrafilainicio1;
                           

                    $objPHPExcel->getActiveSheet()->setCellValue($columnaA,  $modelServicioAlumno->datosMiAlumno);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaB,  $modelServicioAlumno->importe_servicio);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaC,  $modelServicioAlumno->importe_descuento);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaD,  $modelServicioAlumno->importe_abonado);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaE,  $modelServicioAlumno->importeAbonar);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaF,  $modelServicioAlumno->detalleEstadoExcel);
                    
                    
                    $i = $i + 1;
                    $letrafilainicio += 1;
                }  
                
                $carp_cont = \Yii::getAlias('@webroot') . "/archivos_generados"; //carpeta a almacenar los archivos
                $nombre_archivo = "listadoServiciosOfrecidos" . \Yii::$app->user->id . ".xlsx";                                
                $ruta_archivo = $carp_cont . "/" . $nombre_archivo;
            
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
                $writer->save($ruta_archivo);    
                
                $this->downPadron($nombre_archivo);                
            
        
        }catch (\Exception $e) {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error', Yii::$app->params['errorExcepcion']);
            return $this->redirect(['/site/index']);            
        }  
    }    
    
    /***********************************************/
    public function cellColor($objPHPExcel,$cells,$color){
        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array('type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'startcolor' => array('rgb' => $color) ));
    }  
    
 

    public function downPadron($nombArchivo) {   
        ob_start();
        $name = $nombArchivo;        
        $carp_cont = Yii::getAlias('@webroot') . "/archivos_generados"; //carpeta a almacenar los archivos                                       
        $ruta_archivo = $carp_cont . "/" . $name;
        
        if (is_file($ruta_archivo)) {
            ob_get_clean();
            $size = filesize($ruta_archivo);
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=listadoServiciosOfrecidos.xlsx");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . $size); 
            readfile($ruta_archivo);
        }
        exit;
    }    
    
}
