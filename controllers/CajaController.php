<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

/**
 * Description of CajaController
 *
 * @author agus
 */
class CajaController extends \yii\web\Controller {
    
    
    
    
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            // declares "error" action using a class name
            'buscarFamilia' => 'app\actions\BuscarFamiliaAction',
            'agregarServicioImpago' => 'app\actions\BuscarDeudaFamiliaAction',
        ];
    } 
    
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    /*******************************************************************/
    /*******************************************************************/
    public function actionCobrar() {
        try 
        {
            $modelTiket = new \app\models\Tiket(); 
            
            $familiaTiket = null;
                    
            $modelsServiciosTiket = new \app\models\ServiciosTiket();
            
            $selectServicios = Yii::$app->request->get('selectServicios');
            $selectCuotasCP = Yii::$app->request->get('selectCuotasCP');
            
            $servicios = null;
            if(!empty($selectServicios)){               
                $servicios = explode(',', $selectServicios);
                array_unique($servicios);
                $serviciosEnTiket = implode(",", $servicios);
            }else
                $serviciosEnTiket = null;
            
            
            $cuotasCP = null;
            if(!empty($selectCuotasCP)){               
                $cuotas = explode(',', $selectCuotasCP);
                array_unique($cuotas);
                $cuotasEnTiket = implode(",", $cuotas);
            }else
                $cuotasEnTiket = null;
            
            
            
            //servicios adheridos al tiket para su cobro
            //$serviciosEnTiket = [];
            $query = \app\models\ServicioAlumno::find();        
            $query->joinWith('miAlumno a');
            if(!empty($servicios))
                $query->andWhere(['in', 'id', $servicios]);
            
            $providerServicioEnTiket = new \yii\data\ActiveDataProvider([
                'query' => $query,
                'sort' => ['defaultOrder'=>'id desc'],
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
            
            
            $query = \app\models\CuotaConvenioPago::find();        
            if(!empty($cuotas))
                $query->andWhere(['in', 'servicio_alumno.id', $cuotas]);
            
            $providerCuotasTiket = new \yii\data\ActiveDataProvider([
                'query' => $query,
                'sort' => ['defaultOrder'=>'id desc'],
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
          
            
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Atención!!! <br /> Se Produjo un error severo');
            $this->redirect(['/site/index']);
        }
        
        return $this->render('altaFactura', [
            'modelTiket'=>$modelTiket, 
            'serviciosEnTiket'=>$serviciosEnTiket,   
            'providerServicioEnTiket'=>$providerServicioEnTiket,
            'providerCuotasTiket' => $providerCuotasTiket,
            'cuotasEnTiket'=>$cuotasEnTiket
        ]);     
    }
    
    
    
}
