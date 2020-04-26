<?php

namespace app\controllers;

use Yii;
use app\models\Establecimiento;
use app\models\search\EstablecimientoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

use app\models\search\DivisionEscolarSearch;
use app\models\DivisionEscolar;
use app\models\Persona;
use app\models\search\AlumnoSearch;
use app\models\Alumno;
use app\models\ServicioEstablecimiento;

use app\helpers\GralException;

/**
 * EstablecimientoController implements the CRUD actions for Establecimiento model.
 */
class EstablecimientoController extends Controller
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
                        'actions' => ['admin',],
                        'allow' => true,
                        'roles' => ['listarEstablecimientos'],
                    ],
                    [     
                        'actions' => ['alta','update'],
                        'allow' => true,
                        'roles' => ['cargarEstablecimiento'],
                    ],
                    [     
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['eliminarEstablecimiento'],
                    ],
                    [     
                        'actions' => ['view','mis-alumnos'],
                        'allow' => true,
                        'roles' => ['visualizarEstablecimiento'],
                    ],
                    [     
                        'actions' => ['nuevo-servicio','servicios-division','quitar-servicio-division','get-servicios','asignar-servicio-division','mis-servicios-ofrecidos'],
                        'allow' => true,
                         'roles' => ['gestionarServiciosEstablecimiento'],
                    ],
                    [     
                        'actions' => ['cargar-division','actualizar-division','eliminar-division','mis-divisiones-escolares'],
                        'allow' => true,
                        'roles' => ['gestionarDivisionesEscolares'],
                    ],
                    [     
                        'actions' => ['drop-mis-divisionesescolares'],
                        'allow' => true,                        
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
   /************************************************************************/
    /**
     * Finds the Establecimiento model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Establecimiento the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Establecimiento::findOne($id)) !== null) {
            return $model;
        } else {
            throw new GralException('Modelo Establecimiento inexistente.');
        }
    }

    /************************************************************************/
    /************************************************************************/
    /**
     * Deletes an existing Establecimiento model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    { 
        try{$transaction = Yii::$app->db->beginTransaction(); 
            $response = Yii::$app->serviceEstablecimiento->eliminarEstablecimiento($id);
            if($response['success']){  
                $transaction->commit();
                Yii::$app->session->setFlash('success',Yii::$app->params['eliminacionCorrecta']);
                return $this->redirect(['admin']);    
            }else{
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::$app->params['eliminacionErronea']);
                return $this->redirect(['/establecimiento/view', 'id'=>$id]);
            }
        }catch (GralException $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id'=>$id]);        
        }catch (\Exception $e) { 
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
            Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);
            return $this->redirect(['view', 'id'=>$id]);                        
        }
    }

    /************************************************************************/
    /************************************************************************/
    /**
     * Lists all Establecimiento models.
     * @return mixed
     */
    public function actionAdmin()
    {
        try{
            $searchModel = new EstablecimientoSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }catch (\Exception $e) { 
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
            Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);
        }
        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /************************************************************************/
    /************************************************************************/
    /**
     * Creates a new Establecimiento model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAlta()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $model = new Establecimiento();
            if ($model->load(Yii::$app->request->post())) {
                $response = Yii::$app->serviceEstablecimiento->cargarEstablecimiento($model);
                if($response['success']){
                    Yii::$app->session->setFlash('success',Yii::$app->params['cargaCorrecta']);
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $response['nuevoModelo']->id ]);
                }else
                    $model->addErrors($response['error_models']);
            }
        }catch (GralException $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
            throw new GralException($e->getMessage()); 
        }catch (\Exception $e) {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);
            
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    

    /************************************************************************/
    /************************************************************************/
    /**
     * Updates an existing Establecimiento model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $model = $this->findModel($id);
            if ($model->load(Yii::$app->request->post())) {
                $response = Yii::$app->serviceEstablecimiento->actualizarEstablecimiento($id, $model);
                if($response['success']){
                    Yii::$app->session->setFlash('success',Yii::$app->params['cargaCorrecta']);
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                }else
                    $model->addErrors($response['error_models']);
            } 
        }catch (\Exception $e) {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);
        }
        return $this->render('create', [
            'model' => $model,
        ]);

    }
    
    /************************************************************************/
    /************************************************************************/
    /**
     * Displays a single Establecimiento model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        try{            
            $model = $this->findModel($id);
        }catch (\Exception $e) {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));           
            Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);            
        }         
        return $this->render('view', [
            'model' => $this->findModel($id),  
        ]);    
    } 
    
    
    
    /************************************************************************/
    /************************************************************************/
    /**************************** DIVISIONES ESCOLARES **********************/
    public function actionDropMisDivisionesescolares($idEst){
        try{
            $return = '';
            $countDivisiones = DivisionEscolar::find()
                    ->where(['id_establecimiento' => $idEst])
                    ->count();

            $divisiones = DivisionEscolar::find()
                    ->where(['id_establecimiento' => $idEst])
                    ->orderBy('id')->asArray()
                    ->all();   
            
            \Yii::$app->response->format = 'json';
            return $divisiones;                     
        }catch(\Exception $e){    
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
            echo "<option value=''></option>";
        }
         
    }
    
    /************************************************************************/
    /**************************DIVISIONES ESCO*******************************/
    /*
     * Retorna un dataprovider de division escolares pertenecientes 
     * a aun determinado establecimiento parametrizado
     */
    public function actionMisDivisionesEscolares(){
        try{ 
            
            $establecimiento = Yii::$app->request->get('establecimiento');        
            $modelEstablecimiento = $this->findModel($establecimiento);
            if(empty($modelEstablecimiento))
                throw new GralException('No se puden mostrar las divisiones escolares, no existe el Establecimiento.');
        
            //modelo y dataprovider de Divisiones Escolares del Establecimiento
            $searchModelDivisiones = new DivisionEscolarSearch();
            $searchModelDivisiones->id_establecimiento = $establecimiento;
            $dataProviderDivisiones = $searchModelDivisiones->search(Yii::$app->request->queryParams);        
        }catch(GralException $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));   
            throw new GralException($e->getMessage());            
        }catch(\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));                  
            throw new GralException('No se puden mostrar las divisiones escolares.');
        }
        
        return $this->renderAjax('_misDivisionesEscolares', [
            'modelEstablecimiento' => $modelEstablecimiento,                    
            'dataProviderDivisiones' =>$dataProviderDivisiones,
        ]); 
    }    
       
    /*
     * Carga una division escolar en un determinado establecimiento.
     * Renderiza un formulario para la carga de los datos de la division.
     */
    public function actionCargarDivision(){        
        $transaction = Yii::$app->db->beginTransaction(); 
        try{
            $establecimiento = Yii::$app->request->get('establecimiento');        
            $modelEstablecimiento = $this->findModel($establecimiento);
            if(empty($modelEstablecimiento))
                throw new GralException('No se pude realizar la carga de divisiones escolares. El identificador del estabecimiento no existe');
        
            $modelDivisionEscolar = new DivisionEscolar();
            $modelDivisionEscolar->id_establecimiento = $modelEstablecimiento->id;            
            if ($modelDivisionEscolar->load(Yii::$app->request->post())) {
                $response = Yii::$app->serviceEstablecimiento->cargarDivisionEscolar($modelDivisionEscolar);
                if($response['success']){
                    $transaction->commit();
                    Yii::$app->session->setFlash('success',Yii::$app->params['cargaCorrecta']);
                    Yii::$app->response->format = 'json';
                    return ['carga' => '1', 'form' => '0', 'error' => '0', 'mensaje' => Yii::$app->params['cargaCorrecta'], 'id'=>$response['nuevoModelo']->id];
                }else
                    $modelDivisionEscolar->addErrors($response['error_models']);
            } 
        }catch(GralException $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));   
            throw new GralException($e->getMessage());            
        }catch(\Exception $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));                  
            throw new GralException('No se puden mostrar las divisiones escolares.');
        }
        
        //renderizamos las vistas, formulario de carga
        Yii::$app->response->format = 'json';
        return ['form' => '1', 'error'=>'0',
            'vista' => $this->renderAjax('_formDivision', [
                'model' => $modelDivisionEscolar, 
                'modelEstablecimiento'=>$modelEstablecimiento
        ])];
    }
    
    /*
     * Actualiza los datos de una dterminada DivisionEscolar.
     * Renderiza un formulario para la carga de los datos de la division.
     */
    public function actionActualizarDivision(){  
        $transaction = Yii::$app->db->beginTransaction(); 
        try{
            $id = Yii::$app->request->get('id');  
            $model =  DivisionEscolar::findOne($id); 
            if(empty($model))
                throw new GralException('No se pude realizar la actualización, modelo inexistente con el identificador proporcionado.');
                        
            $modelEstablecimiento = Establecimiento::findOne($model->id_establecimiento);
            if(empty($modelEstablecimiento))
                throw new GralException('No se pude realizar la carga de divisiones escolares. El identificador del estabecimiento no existe');
            
            if ($model->load(Yii::$app->request->post())) {
                $response = Yii::$app->serviceEstablecimiento->actualizarDivisionEscolar($id, $model);
                if($response['success']){
                    $mensaje = Yii::$app->params['cargaCorrecta'];
                    $transaction->commit();
                    Yii::$app->response->format = 'json';
                    return ['carga' => '1', 'form' => '0', 'error' => '0', 'mensaje' => $mensaje, 'id'=>$response['nuevoModelo']->id];    
                }else
                    $model->addErrors($response['error_models']);
            }                   
        }catch(\Exception $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', json_encode($e));
            $transaction->rollBack();
            Yii::$app->response->format = 'json';
            return ['error' => '1', 'message' => Yii::$app->params['errorExcepcion']];
        }
        
        //renderizamos las vistas, formulario de carga
        Yii::$app->response->format = 'json';
        return  ['form' => '1', 'error'=>'0',
            'vista' => $this->renderAjax('_formDivision', [
                    'model' => $model,
                    'modelEstablecimiento'=>$modelEstablecimiento
        ])];  
    } 
      
    public function actionEliminarDivision()
    {
        $transaction = Yii::$app->db->beginTransaction(); 
        try{
            $id = Yii::$app->request->get('id');       
            $response = Yii::$app->serviceEstablecimiento->eliminarDivisionEscolar($id);
            if($response['success']){            
                $transaction->commit();
                Yii::$app->response->format = 'json';
                return ['error' => '0', 'message' => Yii::$app->params['eliminacionCorrecta']];
            }else{
               $transaction->rollBack();
                Yii::$app->response->format = 'json';
                return ['error' => '1', 'message' => Yii::$app->params['eliminacionErronea']];
            }
        }catch(GralException $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));   
            throw new GralException($e->getMessage());            
        }catch(\Exception $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));                  
            throw new GralException('Error grave al intentar procesar la solcitud de eliminacion de division escolar.');
        }
    }
    
    /************************************************************************/
    /************************************************************************/
    /**************************** SERVICIOS OFRECIDOS  **********************/
    public function actionMisServiciosOfrecidos(){
        try{
            $establecimiento = Yii::$app->request->get('establecimiento');        
            $modelEstablecimiento = $this->findModel($establecimiento);
            if(empty($modelEstablecimiento))
                throw new GralException('No se puden mostrar las servicios asociados al establecimiento. Id del estabecimiento no existe');
            
            //modelo y dataprovider de Servicios Establecimiento
            $searchModelServiciosEstablecimiento = new \app\models\search\ServicioDivisionEscolarSearch();
            $searchModelServiciosEstablecimiento->establecimiento = $establecimiento;
            $dataProviderSerEst = $searchModelServiciosEstablecimiento->search(Yii::$app->request->queryParams);  
            
        }catch(GralException $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw new GralException($e->getMessage());            
        }catch(\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));                  
            throw new GralException('No se puden mostrar las servicios asociados al establecimiento. Error grave');
        }
        
        return $this->renderAjax('_misServicios', [
            'modelEstablecimiento' => $modelEstablecimiento,                    
            'dataProviderSerEst' =>$dataProviderSerEst,
            'searchModelSerEst' => $searchModelServiciosEstablecimiento,                 
        ]); 
    }
    
    public function actionNuevoServicio(){        
        $modelEstablecimiento = $this->findModel(Yii::$app->request->get('id_establecimiento'));        
        try{
            $model = new \app\models\ServicioDivisionEscolar();
            $model->establecimiento  = $modelEstablecimiento->id;           
            
            $queryDivisiones = DivisionEscolar::find()->andWhere(['id_establecimiento' => $modelEstablecimiento->id]);            
            $dataProviderDivisiones = new ActiveDataProvider([
                'query' => $queryDivisiones,           
                'pagination' => false
            ]);
        }catch (\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', json_encode($e));            
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'NO SE PUDO CARGAR EL SERVICIO AL ESTABLECIMIENTO');
        }
        
        Yii::$app->response->format = 'json';
        return ['form' => '1', 'error'=>'0',
            'vista' => $this->renderAjax('asignarServicio', [
            'modelEstablecimiento'=>$modelEstablecimiento,
            'model' => $model,
            'dataProviderDivisiones'=>$dataProviderDivisiones
        ])];        
    }    
    
    /*
     * Dadoun determinado estableciminetoy un identificador de servicioofrecido;
     * busca las divisiones escolares que tengan asciadas dicho servicio.
     * Esto es aquellas divisiones a las que se lehaya otorgado dicho servicio ofecido
     * para devengar a asus alumnos.
     * 
     * @params idEstablecimiento
     * @params idServicio
     */    
    public function actionGetServicios(){
        try{
            $modelEstablecimiento = $this->findModel(Yii::$app->request->get('idEstablecimiento'));
        
            $modelServicio = \app\models\ServicioOfrecido::findOne(Yii::$app->request->get('idServicio'));
            if(empty($modelServicio))
               throw new GralException('Servicio Ofrecido inexistente.');
        
            
            $queryDivisiones = DivisionEscolar::find()->andWhere(['id_establecimiento' => $modelEstablecimiento->id]);            
            $dataProviderDivisiones = new ActiveDataProvider([
                'query' => $queryDivisiones,           
                'pagination' => false
            ]);
            
            $queryDivisionesConServicio = DivisionEscolar::find()->joinWith(['miServicios s']);
            $divisionesConServicio = $queryDivisionesConServicio 
                ->andFilterWhere(['id_establecimiento' => $modelEstablecimiento->id])
                ->andFilterWhere(['s.id_servicio' => $modelServicio->id])->asArray()->all();
            $divisionesConServicio = \yii\helpers\ArrayHelper::map($divisionesConServicio, 'id','nombre');
         
            Yii::$app->response->format = 'json';
            return ['error' => '0',
                'vista' => $this->renderAjax('_divisiones-del-servicio', [
                    'modelEstablecimiento'=>$modelEstablecimiento,
                    'modelServicio'=>$modelServicio,
                    'dataProviderDivisiones' => $dataProviderDivisiones,
                    'divisionesConServicio' => $divisionesConServicio,                    
            ])];
        }catch (GralException $e) {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));   
           throw new GralException($e->getMessage());
        }catch (\Exception $e) {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));    
           throw new GralException(500, 'Error Servero' . $e->getMessage());            
        } 
            Yii::$app->response->format = 'json';
            return ['error' => '0',
                'vista' => $this->renderAjax('_divisiones-del-servicio', [
                    'modelEstablecimiento'=>$modelEstablecimiento,
                    'modelServicio'=>$modelServicio,
                    'dataProviderDivisiones' => $dataProviderDivisiones,
                    'divisionesConServicio' => $divisionesConServicio,                    
            ])];
    }   
 
   
    /*
     * Asigna un determinado servicio (ServicioOfrecido) a una determinada division escolar.
     */
    
    public function actionAsignarServicioDivision()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $division = Yii::$app->request->get('division');
            $servicio = Yii::$app->request->get('servicio');

            $response = Yii::$app->serviceServicioOfrecido->asociarDivisionEscolarAlServicio($servicio, $division);
            if($response['success']){
                $transaction->commit();
                Yii::$app->response->format = 'json';
                return ['error' => '0'];                
            }else{
                $transaction->rollBack();
                Yii::$app->response->format = 'json';
                return ['error' => '1'];         
            }   
        }catch (GralException $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));      
            throw new GralException('No se puden realizar la operación.' . $e->getMessage());            
        }catch (\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));      
            throw new GralException('No se puden realizar la operación.');            
        }
    }    
    
    /*
     * Elimina un determinado servicio (ServicioOfrecido) a una determinada division escolar.
     */    
    public function actionQuitarServicioDivision()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $division = Yii::$app->request->get('division');
            $servicio = Yii::$app->request->get('servicio');
            
            $response = Yii::$app->serviceServicioOfrecido->quitarDivisionEscolarAlServicio($servicio, $division);
            if($response['success']){
                $transaction->commit();
                //Yii::$app->session->setFlash('success', 'Se asocio correctamente la división al Servicio');
                Yii::$app->response->format = 'json';
                return ['error' => '0'];        
            }else{
                $transaction->rollBack();
                //Yii::$app->session->setFlash('error', 'No se pudo asociar la división al Servicio');
                Yii::$app->response->format = 'json';
                return ['error' => '1'];
            }  
        }catch (GralException $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));      
            throw new GralException('No se puden realizar la operación.' . $e->getMessage());            
        }catch (\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));      
            throw new GralException('No se puden realizar la operación.');            
        }   
    }   
    
    /************************************************************************/
    /************************************************************************/
    /**************************** SERVICIOS OFRECIDOS  **********************/
    /*
     * Retorna un dataprovider de alumnos pertenecientes 
     * a aun determinado establecimiento parametrizado
     */
    public function actionMisAlumnos(){
        try{
            $establecimiento = Yii::$app->request->get('establecimiento');        
            $modelEstablecimiento = $this->findModel($establecimiento);
            if(empty($modelEstablecimiento))
                throw new GralException('No se puden mostrar los alumnos del establecimiento. Id del estabecimiento no existe');
            
            //modelo y dataprovider de Alumnos del Establecimiento
            $modelPersona =  new Persona();
            $modelPersona->load(Yii::$app->request->queryParams); 
            $searchModelAlumnos = new AlumnoSearch();
            $searchModelAlumnos->establecimiento = $modelEstablecimiento->id;
            $searchModelAlumnos->activo = 1;
            $dataProviderAlumnos = $searchModelAlumnos->search(Yii::$app->request->queryParams,$modelPersona);

             
        }catch(GralException $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw new GralException('No se puden mostrar los alumnos asociados al establecimiento. ' . $e->getMessage());            
        }catch(\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));                 
            throw new GralException('No se puden mostrar los alumnos asociados al establecimiento. Error grave');
        }
        
        return $this->renderAjax('_misAlumnos', [
            'modelEstablecimiento' => $modelEstablecimiento,                    
            'dataProviderAlumnos' =>$dataProviderAlumnos,
            'searchModelAlumnos' => $searchModelAlumnos,   
            'modelPersona' => $modelPersona,
        ]);
        
            
    }
 

    
    
}
