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
    
    
    
    
    /**
     * @inheritdoc
     */
    /*
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [ 
                    [     
                        'actions' => ['cobrar',''],
                        'allow' => true,              
                        'roles' => ['egresarAlumnos'],
                    ],
                ],
                'denyCallback' => function($rule, $action){           
                    if(Yii::$app->request->isAjax){                       
                        throw new GralException('Acceso denegado, usted no dispone de los permisos suficienes para realizar la acción');
                    }else{   
                        throw new \yii\web\ForbiddenHttpException();
                    }
                }
            ],  
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['GET'],
                ],
            ],
        ];
    }*/
    
    
    
    
    
    public function actions()
    {
        return [
            'buscarFamilia' => 'app\actions\BuscarFamiliaAction',
            'buscarDeudaFamilia' => 'app\actions\BuscarDeudaFamiliaAction',
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
            
            if($modelTiket->load(Yii::$app->request->post())){
                $response = Yii::$app->serviceCaja->generarTiket($modelTiket);
                if($response['success']){                      
                    Yii::$app->session->setFlash('success', Yii::$app->params['eliminacionCorrecta']);  
                    $idTiket = $response['modelsTiket']->id;
                    return $this->redirect(['detalle-tiket', 'id'=>$idTiket]);
                }else{                    
                    Yii::$app->session->setFlash('error', Yii::$app->params['eliminacionErronea']);                   
                }    
            }
        }catch (\Exception $e) {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error', 'Atención!!! <br /> Se Produjo un error severo');
            $this->redirect(['/site/index']);
        }
        
        return $this->render('formCobro', [
            'modelTiket'=>$modelTiket, 
            
        ]);     
    }
    
    public function actionCobrarServicios() {
        try 
        {
            $modelTiket = new \app\models\Tiket(); 
            $familiaTiket = null;
            
            if($modelTiket->load(Yii::$app->request->post())){
                $selectServicios = Yii::$app->request->post('servicios-tiket');
                $selectCuotasCP = Yii::$app->request->post('cuotascp-tiket');
                $servicios = null;
                if(!empty($selectServicios)){               
                    $servicios = explode(',', $selectServicios);
                    array_unique($servicios);
                }
            
                $cuotasCP = null;
                if(!empty($selectCuotasCP)){               
                    $cuotasCP = explode(',', $selectCuotasCP);
                    array_unique($cuotasCP);
                }
            
                $response = Yii::$app->serviceCaja->generarTiket($modelTiket, $servicios,$cuotasCP);
                if($response['success']){                      
                    Yii::$app->session->setFlash('success', Yii::$app->params['eliminacionCorrecta']);  
                    $idTiket = $response['modelsTiket']->id;
                    return $this->redirect(['detalle-tiket', 'id'=>$idTiket]);
                }else{                    
                    Yii::$app->session->setFlash('error', Yii::$app->params['eliminacionErronea']);                   
                }    
            }
            
            
            $selectServicios = Yii::$app->request->get('selectServicios');
            $selectCuotasCP = Yii::$app->request->get('selectCuotasCP');
            $familia = Yii::$app->request->get('familia');
            
            $servicios = null;
            if(!empty($selectServicios)){               
                $servicios = explode(',', $selectServicios);
                array_unique($servicios);
                $serviciosEnTiket = implode(",", $servicios);
            }else
                $serviciosEnTiket = null;
            
            
            $cuotasCP = null;
            if(!empty($selectCuotasCP)){               
                $cuotasCP = explode(',', $selectCuotasCP);
                array_unique($cuotas);
                $cuotasEnTiket = implode(",", $cuotas);
            }else
                $cuotasEnTiket = null;
            
            
            if(!empty($familia))
                $dataProvider =  Yii::$app->serviceServicioAlumno::devolverDeudaFamilia($familia, null,(is_null($serviciosEnTiket))?0:$serviciosEnTiket, (is_null($cuotasEnTiket))?0:$cuotasEnTiket);
            else
                $dataProvider = '';
           
          
            
        } catch (\Exception $e) {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error', 'Atención!!! <br /> Se Produjo un error severo');
            $this->redirect(['/site/index']);
        }
        
        return $this->render('formTiketCobroServicios', [
            'modelTiket'=>$modelTiket, 
            'serviciosEnTiket'=>$serviciosEnTiket,               
            'cuotasEnTiket'=>$cuotasEnTiket,
            'dataProvider'=>$dataProvider
        ]);     
    }
    
    
    
    
    public function actionDetalleTiket($id){
        try{
            $idTiket = Yii::$app->request->get('id');
            $modelTiket = \app\models\Tiket::findOne($idTiket);
            if(!$modelTiket)
                throw  new GralException('No se encontró el modelo del Tiket');
            
            $modelFactura = \app\models\Factura::find()->andWhere(['id_tiket'=>$modelTiket->id])->one();
            if(!$modelFactura)
                $modelFactura = null;
            
            $serviciosTiket = \app\models\ServiciosTiket::find()->andWhere(['id_tiket'=>$modelTiket->id_factura])->all();
            
        }catch (GralException $e) {   
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            throw new GralException($e->getMessage());            
        }catch (\Exception $e){ 
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw new \yii\web\HttpException(500, $e->getMessage());
        }  
        
        return $this->render('detalleTiket', [
            'modelTiket'=>$modelTiket,
            'modelFactura'=>$modelFactura,   
            'serviciosTiket'=>$serviciosTiket
        ]);   
    }
    
}
