<?php

namespace app\controllers;

use Yii;
use app\models\TipoResponsable;
use app\models\search\TipoResponsableSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\helpers\GralException;
/**
 * TipoResponsableController implements the CRUD actions for TipoResponsable model.
 */
class TipoResponsableController extends Controller
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
                        'roles' => ['gestionarTipoResponsable'],
                    ],
                ],
            ], 
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    /****************************************************************/
    /**
     * Deletes an existing TipoResponsable model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try{
            $transaction = Yii::$app->db->beginTransaction(); 
            if($id>5){  
                if ( $this->findModel($id)->delete() ){
                    $transaction->commit();
                    if (Yii::$app->request->isAjax){                    
                        Yii::$app->response->format = 'json';
                        return ['error' => '0', 'mensaje' => Yii::$app->params['eliminacionCorrecta']];
                    }else{
                        Yii::$app->session->setFlash('ok',Yii::$app->params['eliminacionCorrecta']);
                        return $this->redirect(['index']);
                    }
                }
            }else{
                $mensaje = 'No se puede realizar la eliminación. El registro se encuentra bloqueado';
                throw new GralException($mensaje);
                //Yii::$app->response->format = 'json';
                //return ['error' => '1', 'mensaje' => 'No se puede realizar la eliminación. El registro se encuentra bloqueado'];    
            }
        }catch(GralException $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw new \yii\web\HttpException(null, $e->getMessage());
        }catch(\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));            
            if (Yii::$app->request->isAjax){
                throw new GralException(Yii::$app->params['errorExcepcion']);
            }else{
                Yii::$app->session->setFlash('error', Yii::$app->params['errorExcepcion']);
                return $this->redirect(['index']);
            }            
        }
    }
    
    /*****************************************************************/
    /*****************************************************************/
    /**
     * Creates a new TipoResponsable model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function  actionCreate()
    {
        try{
            $transaction = Yii::$app->db->beginTransaction();
            $id = Yii::$app->request->get('id');        
            $mensaje = '';
            
            if(!empty($id)){
                if($id<=5){
                    $mensaje = 'No se puede actualizar el registro, el mismo se encuentra bloqueado';    
                    throw new GralException($mensaje);
                }
                $model = $this->findModel($id);
            }
            else
                $model = new TipoResponsable();

            if ($model->load(Yii::$app->request->post())) {                
                ($model->isNewRecord)?$mensaje = Yii::$app->params['cargaCorrecta']:$mensaje = Yii::$app->params['actualizacionCorrecta'];
                    
                if ($model->save()){                    
                    $transaction->commit();
                    if (Yii::$app->request->isAjax){
                        Yii::$app->response->format = 'json';
                        return ['carga' => '1', 'form'=>'0', 'error' => '0', 'mensaje' => $mensaje, 'id'=>$model->id];
                    }else{
                        Yii::$app->session->setFlash('ok',$mensaje);
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }else
                    $mensaje = Yii::$app->params['operacionFallida'];
            }
            
            //renderizamos las vistas, formulario de carga
            if (Yii::$app->request->isAjax){
                Yii::$app->response->format = 'json';
                return ['form' => '1', 'error' => '0', 'mensaje' => $mensaje,
                        'vista' => $this->renderAjax('create', ['model' => $model]
                    )];                
            }else{
                return $this->render('create', [
                    'model' => $model,
                ]);
            }            
        }catch(GralException $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw new GralException($e->getMessage());
        }catch(\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));            
            if (Yii::$app->request->isAjax){
                throw new GralException(Yii::$app->params['errorExcepcion']);
            }else{
                Yii::$app->session->setFlash('error',Yii::$app->params['errorExcepcion']);                
            }            
        }        
    } //fin createAjax

    
    /*****************************************************************/
    /*****************************************************************/
    /**
     * Lists all TipoResponsable models.
     * @return mixed
     */
    public function actionIndex()
    {
        try{
            $searchModel = new TipoResponsableSearch;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);    
        }catch(\Exception $e){
           \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
            Yii::$app->session->setFlash('error',Yii::$app->params['errorExcepcion']);
            return $this->redirect(['site/index']);    
        }
        
    }
    
    
    /*********************************************************************/
    /**
     * Displays a single TipoSexp model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
 
    /**
     * Finds the TipoResponsable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TipoResponsable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TipoResponsable::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}