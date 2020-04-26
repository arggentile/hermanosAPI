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

use app\helpers\GralException;


/**
 * Description of EditarServicioAlumnoAction
 *
 * @author agus
 */
class EditarServicioAlumnoAction extends Action{
    
    
    public function run()
    {       
        try{   
            $transaction = Yii::$app->db->beginTransaction(); 
            $idservicioalumno = Yii::$app->request->get('idservicio');
            $model = models\ServicioAlumno::findOne($idservicioalumno);
            if(!$model)
                throw new GralException('Servicio inexistenete');
                    
            if ($model->load(Yii::$app->request->post())) {
                $response = Yii::$app->serviceServicioAlumno->editarServicioAlumno($idservicioalumno, $model);
                if($response['success']){
                    $transaction->commit();                                    
                    Yii::$app->response->format = 'json';
                    return ['error' => '0', 'success' => true,'mensaje' => 'Se edito Correctamente!!!'];           
                    
                }else
                    $model->addErrors($response['error_models']);
                
                if(Yii::$app->request->isAjax){
                $response['form']='1';
                    $response['vista'] = $this->controller->render('@app/actions/views/editarServicio', 
                                                    ['model'=> $model]);
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $response;    
            }else    
                return $this->controller->renderAjax('@app/actions/views/editarServicio', 
                    ['model'=> $model]);
                
            }

            
        }catch (GralException $e) { 
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));          
            throw new GralException($e->getMessage());                        
        }catch (\Exception $e) { 
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));            
            throw new GralException('Error grave al intentar modificar los datos del servicio.');                        
        }
        return $this->controller->renderAjax('@app/actions/views/editarServicio', 
                    ['model'=> $model]);
    }    
}
