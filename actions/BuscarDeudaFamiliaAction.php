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

use app\models\search\GrupoFamiliarSearch;
use app\widgets\buscadorfamilia\BuscadorFamilia;
use app\models\GrupoFamiliar;
use app\models\search\ServicioAlumnoSearch;
use app\models\ServicioAlumno;

use app\helpers\GralException;

class BuscarDeudaFamiliaAction extends Action
{
    public function run()
    {       
        try{           
            $familia = Yii::$app->request->get('familia');
            
            $searchModel = new models\DeudaFamiliaForm();
            if(!empty($searchModel))
                $searchModel->id_familia = $familia;
            
            $searchModel->load(\Yii::$app->request->getQueryParams()); 
            
            $dataProvider =  Yii::$app->serviceServicioAlumno::devolverDeudaFamilia($searchModel->id_familia);
            return $this->controller->renderAjax('@app/actions/clean/views/clean', ['contenido'=> \app\widgets\buscadorDeudaFamilia\BuscadorDeudaFamilia::widget(['searchModel'=>$searchModel, 'dataProvider'=>$dataProvider])]);
        }catch (GralException $e) { 
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));          
            throw new GralException('No se puden mostrar las familias.');                        
        }catch (\Exception $e) { 
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));            
            throw new GralException('No se puden mostrar las familias.');                        
        }
    }    
    
}
