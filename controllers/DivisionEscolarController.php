<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\DivisionEscolar;
use app\models\search\DivisionEscolarSearch;
use app\models\Persona;
use app\models\Alumno;
use app\models\search\AlumnoSearch;

use app\helpers\GralException;

/**
 * DivisionEscolarController implements the CRUD actions for DivisionEscolar model.
 */
class DivisionEscolarController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [                        
                        'allow' => true,
                        'roles' => ['gestionarDivisionesEscolares'],
                    ],
                    
                ],
                'denyCallback' => function($rule, $action){ 
                    if(Yii::$app->request->isAjax)
                            throw new GralException('Acceso denegado, usted no dispone de los permisos suficienes para realizar la accion');
                    else
                        return $this->redirect(['/site']);         
                }
                
            ],  
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
        
    
    /*******************************************************************/
    /*******************************************************************/
    /**
     * Displays a single DivisionEscolar model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $modelDivision=$this->findModel($id);
        
        //model y dataprovider de Alumnos
        $modelPersona =  new Persona();
        $modelPersona->load(Yii::$app->request->queryParams); 
        $searchModelAlumnos = new AlumnoSearch();
        $searchModelAlumnos->id_divisionescolar = $id;
        $dataProviderAlumnos = $searchModelAlumnos->search(Yii::$app->request->queryParams,$modelPersona);
    
        //servicios de mis alumnos
        $searchModelSerAlumnos = new \app\models\search\ServicioAlumnoSearch();
        $searchModelSerAlumnos->division_escolar = $id;
        $dataProviderSerAlumnos = $searchModelSerAlumnos->search(Yii::$app->request->post());        
        
        return $this->render('view', [
                'model' => $modelDivision,
                //model y dataprovider de Alumnos
                'modelPersona'=> $modelPersona,
                'dataProviderAlumnos' =>$dataProviderAlumnos,
                'searchModelAlumnos' => $searchModelAlumnos,
                 //servicios de mis alumnos
                'searchModelSerAlumnos'=>$searchModelSerAlumnos,
                'dataProviderSerAlumnos'=>$dataProviderSerAlumnos
            
        ]);
    }

       

    /**
     * Finds the DivisionEscolar model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DivisionEscolar the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DivisionEscolar::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
