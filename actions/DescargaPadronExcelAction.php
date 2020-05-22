<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\actions;

use Yii;
use yii\base\Action;
use yii\base\Exception;
use yii\base\UserException;
use app\models;
use yii\data\ActiveDataProvider;

/**
 * Description of DescargaPadronExcelAction
 *
 * @author agus
 */
class DescargaPadronExcelAction extends Action {
    
    public function run()
    {       
       
            $archivo = Yii::$app->request->get('archivo');     
            ob_start();
            //$name = $_GET["id"];        
            $name = $archivo;
            $carp_cont = Yii::getAlias('@archivos');
            $ruta_archivo = $carp_cont . "/" . $name;

            if (is_file($ruta_archivo)) {
                ob_get_clean();
                $size = filesize($ruta_archivo);
                header("Content-Type: application/vnd.ms-excel");
                header("Content-Disposition: attachment; filename=listadoAlumnos.xlsx");
                header("Content-Transfer-Encoding: binary");
                header("Content-Length: " . $size);
                readfile($ruta_archivo);
            }
            exit;
        
    }   
}
