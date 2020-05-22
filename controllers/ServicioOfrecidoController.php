<?php

namespace app\controllers;

use Yii;
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

use app\helpers\GralException;

/**
 * ServicioOfrecidoController implements the CRUD actions for ServicioOfrecido model.
 */
class ServicioOfrecidoController extends Controller
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
                        'actions' => ['admin','delete','view','alta','update'], 
                        'allow' => true,
                        'roles' => ['gestionarServicios'],
                    ],
                    [      
                        'actions' => ['divisiones-asociadas','asociar-division','asignar-servicio-division','quitar-servicio-division'],   
                        'allow' => true,
                        'roles' => ['gestionarServicios'],
                    ],                   
                    [     
                        'actions' => ['devengar-servicio','eliminar-devengamiento'],
                        'allow' => true,       
                        'roles' => ['devengarServicioOfrecido'],
                    ],
                    [      
                        'actions' => ['action-editar-serivicio-alumno','down-padron-excel'],   
                        'allow' => true,
                        //'roles' => ['editarServicioAlumno'],
                    ],
                    
                ],
                'denyCallback' => function($rule, $action){ 
                    if(Yii::$app->request->isAjax)
                        throw new GralException('Acceso denegado, usted no dispone de los permisos suficienes para realizar la acción');
                    else
                        return $this->redirect(['site']);         
                }
            ],  
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST','GET'],
                ],
            ],
        ];
    }

    
    public function actions()
    {
        return [
            //accion comun para descargar excel
            'down-padron-excel'=>'app\actions\DescargaPadronExcelAction',
        ];
    } 
    
    /*******************************************************************/
    /**
     * Finds the ServicioOfrecido model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ServicioOfrecido the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ServicioOfrecido::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    
    /******************************************************************/
    /******************************************************************/
    /**
     * Lists all ServicioOfrecido models.
     * @return mixed
     */
    public function actionAdmin()
    {
        try{
            $export = Yii::$app->request->get('export');
            if(isset($export) && $export==1)
                return $this->exportarListado();
            
            $searchModel = new ServicioOfrecidoSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            
            $data = [];     
            $data['filtros']['tiposervicios'] = \yii\helpers\ArrayHelper::map(\app\models\CategoriaServicioOfrecido::find()->asArray()->all(), 'id', 'descripcion');
            $data['filtros']['sino'] = ['0'=>'No', '1'=>'Si'];
        }catch (\Exception $e) {           
           \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
           \Yii::$app->session->setFlash('error', Yii::$app->params['errorExcepcion']);
        }   
        
        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'data'=>$data
        ]);
    } 
    

    /**
     * Deletes an existing ServicioOfrecido model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try{            
            $transaction = Yii::$app->db->beginTransaction(); 
            $response = Yii::$app->serviceServicioOfrecido->eliminarServicioOfrecido($id);
            if($response['success']){    
                $transaction->commit();
                Yii::$app->session->setFlash('success',Yii::$app->params['eliminacionCorrecta']);
                return $this->redirect(['admin']);    
            }else{
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::$app->params['eliminacionErronea']);
                return $this->redirect(['admin']);
            }
        }catch (GralException $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            \Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->request->referrer); 
        }catch (\Exception $e) { 
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            \Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);
            return $this->redirect(Yii::$app->request->referrer);                        
        }
    }
    

    /**
     * Displays a single ServicioOfrecido model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        try{
            $modelServicioOfrecido = $this->findModel($id);

            $searchModelServicioAlumnos = new \app\models\search\ServicioAlumnoSearch();
            $searchModelServicioAlumnos->id_servicio = $id;
            $dataProviderSerAlumnos = $searchModelServicioAlumnos->search(Yii::$app->request->get());            
        }catch (\Exception $e) {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));              
            Yii::$app->session->setFlash('error',Yii::$app->params['operacionFallida']);
        }
        return $this->render('view', [
            'model' => $modelServicioOfrecido,
            'searchModelSerAlumnos'=>$searchModelServicioAlumnos,
            'dataProviderSerAlumnos'=>$dataProviderSerAlumnos
        ]);
    } // fin delview

    
    /**
     * Creates a new ServicioOfrecido model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAlta()
    {               
        $transaction = Yii::$app->db->beginTransaction(); 
        try{
            $model = new ServicioOfrecido();

            if ($model->load(Yii::$app->request->post())) {
                $response = Yii::$app->serviceServicioOfrecido->cargarServicioOfrecido($model);
                if($response['success']){
                    Yii::$app->session->setFlash('success',Yii::$app->params['cargaCorrecta']);
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $response['nuevoModelo']->id ]);
                }else
                    $model->addErrors($response['error_models']);
            }
        }catch (\Exception $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);
        }
        
        return $this->render('create', [
            'model' => $model,
        ]);
    } 
    
    /**
     * Updates an existing ServicioOfrecido model.
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
                $response = Yii::$app->serviceServicioOfrecido->actualizarServicioOfrecido($id,$model);
                if($response['success']){
                    Yii::$app->session->setFlash('success',Yii::$app->params['actualizacionCorrecta']);
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $response['nuevoModelo']->id ]);
                }else
                    $model->addErrors($response['error_models']);
            } 
        }catch (\Exception $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    } 
    
    
    /******************************************************************/
    /*
     * Despliega un listado con las divisiones asignadas que pueden ser devengadas el servicio
     * a los alumnos integrantes de dicha division escolar
     */
    public function actionDivisionesAsociadas(){
        try{            
            $id = Yii::$app->request->get('id');
            $model = $this->findModel($id); 
            $modeDivisionesAsociadas = $model->servicioDivisionescolars;           
        }catch (\Exception $e) {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            $transaction->rollBack(); 
            Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);
        }
        return $this->render('divisiones-asociadas', [
            'model' => $model,
            'modeDivisionesAsociadas' => $modeDivisionesAsociadas
        ]);
    }
    
    public function actionAsociarDivision(){        
        try{
            $modelServicioOfrecido = $this->findModel(Yii::$app->request->get('id'));        
        
            
            $serviciosDivisiones = \app\models\ServicioDivisionEscolar::find()->andWhere(['id_servicio'=>$modelServicioOfrecido->id])->asArray()->all();
            $serviciosDivisiones = \yii\helpers\ArrayHelper::getColumn($serviciosDivisiones, 'id_divisionescolar');
            
            $queryDivisiones = \app\models\DivisionEscolar::find();            
            $dataProviderDivisiones = new \yii\data\ActiveDataProvider([
                'query' => $queryDivisiones,           
                'pagination' => false
            ]);
        }
        catch (\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));           
            throw new \yii\web\HttpException(null, 'Error interno procesando la solicitud de carga');  
        }
        
        Yii::$app->response->format = 'json';
        return ['form' => '1', 'error'=>'0',
            'vista' => $this->renderAjax('asignarDivision', [            
            'modelServicioOfrecido' => $modelServicioOfrecido,
            'dataProviderDivisiones'=>$dataProviderDivisiones,
            'serviciosDivisiones'=>$serviciosDivisiones
        ])];            
    }

    /*
     * Asigna un determinado servicio (ServicioOfrecido) a una determinada division escolar.
     */
    public function actionAsignarServicioDivision()
    {
        try{            
            $transaction = Yii::$app->db->beginTransaction(); 
            $division = Yii::$app->request->get('division');
            $servicio = Yii::$app->request->get('servicio');
            
            $response = Yii::$app->serviceServicioOfrecido->asociarDivisionEscolarAlServicio($servicio, $division);
            if($response['success']){    
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Se asocio correctamente la división al Servicio');
                Yii::$app->response->format = 'json';
                return ['error' => '0'];        
            }else{
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'No se pudo asociar la división al Servicio');
                Yii::$app->response->format = 'json';
                return ['error' => '1'];
            }
        }catch (GralException $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->request->referrer);  
        }catch (\Exception $e) { 
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);
            return $this->redirect(Yii::$app->request->referrer);                        
        }   
    }    
    
    /*
     * Elimina un determinado servicio (ServicioOfrecido) a una determinada division escolar.
     */
    public function actionQuitarServicioDivision()
    {
        try{            
            $transaction = Yii::$app->db->beginTransaction(); 
            $division = Yii::$app->request->get('division');
            $servicio = Yii::$app->request->get('servicio');
            
            $response = Yii::$app->serviceServicioOfrecido->quitarDivisionEscolarAlServicio($servicio, $division);
            if($response['success']){    
                \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString('asdasda'));
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Se asocio correctamente la división al Servicio');
                Yii::$app->response->format = 'json';
                return ['error' => '0'];        
            }else{
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'No se pudo asociar la división al Servicio');
                Yii::$app->response->format = 'json';
                return ['error' => '1'];
            }
        }catch (GralException $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->request->referrer);  
        }catch (\Exception $e) { 
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);
            return $this->redirect(Yii::$app->request->referrer);                        
        }
    }   

    /**********************************************************************/
    /**********************************************************************/
    /******************************************************************/
    /******************* DEVENGAMIENTO DE SERVICIOS *******************/ 
    /**
     * Funcion que se encarga de devengar el servicio a los alumnos de las distintas divisiones
     * asignadas al periodo en cuestion.
     */
    public function actionDevengarServicio() {
        $transaction = Yii::$app->db->beginTransaction(); 
        try{
            $id = Yii::$app->request->get('id');
            
            $response = Yii::$app->serviceServicioOfrecido->devengarServicio($id);
            if($response['success']){    
                $transaction->commit();
                Yii::$app->response->format = 'json';
                return ['error' => '0','resultado'=>'Se realizó el devengamiento exitosamente.'];        
            }else{
                $transaction->rollBack();                
                Yii::$app->response->format = 'json';
                return ['error' => '1', 'resultado'=>'No se pudo realizar el devengamiento.'];
            }
        }catch (GralException $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            \Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->request->referrer);  
        }catch (\Exception $e) { 
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);
            return $this->redirect(Yii::$app->request->referrer);                        
        }
    } 

    public function actionEliminarDevengamiento(){
        try{            
            $transaction = Yii::$app->db->beginTransaction();             
            $id = Yii::$app->request->get('id');
            
            $response = Yii::$app->serviceServicioOfrecido->quitarDevengarServicio($id);
            if($response['success']){    
                $transaction->commit();
                //Yii::$app->session->setFlash('success', 'Devengamiento éxitoso');
                Yii::$app->response->format = 'json';
                return ['error' => '0', 'resultado'=>'Se realizó la quita del devengamiento exitosamente.'];        
            }else{
                $transaction->rollBack();
                //Yii::$app->session->setFlash('error', 'No se pudo realizar el devengamiento.');
                Yii::$app->response->format = 'json';
                return ['error' => '1', 'resultado'=>'No se pudo realizar la quita del devengamiento.'];
            }
        }catch (GralException $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->request->referrer);  
        }catch (\Exception $e) { 
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);
            return $this->redirect(Yii::$app->request->referrer);                        
        }
    } //fin DevengamientoMatriculas
    
    
    
    
    /***********************************************/
    public function cellColor($objPHPExcel,$cells,$color){
        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array('type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'startcolor' => array('rgb' => $color) ));
    }  
    
    public function exportarListado() {
        try{
          
            $searchModel = new ServicioOfrecidoSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->setPagination(false);
                                   
            $data = $dataProvider->getModels();
            
            $i = 0;                        
            $contador = count($data);

            if ($contador > 0) {
             
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
                
                
                $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Categoria Servicio');
                $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Nombre');
                $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Periodo');
                $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Fecha Vencimiento Pago');
                $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Importe');
                $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Importe H.Prof');
                $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Devenga Automatico');
                $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Activo');
                
                
                
                $letracolumnainicio = 'A';
                $letrafilainicio = 3;
     
                while ($i < $contador) {
                    $letrafilainicio1 = (string) $letrafilainicio;
                    $columnaA = 'A' . $letrafilainicio1;
                    $columnaB = 'B' . $letrafilainicio1;
                    $columnaC = 'C' . $letrafilainicio1;
                    $columnaD = 'D' . $letrafilainicio1;
                    $columnaE = 'E' . $letrafilainicio1;
                    $columnaF = 'F' . $letrafilainicio1;
                    $columnaG = 'G' . $letrafilainicio1;        
                    $columnaH = 'H' . $letrafilainicio1;        

                    $objPHPExcel->getActiveSheet()->setCellValue($columnaA, $data[$i]["miTiposervicio"]["descripcion"]);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaB, $data[$i]["nombre"]);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaC, $data[$i]["detallePeriodo"]);
                   
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaD, $data[$i]["xfecha_vencimiento"]);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaE, $data[$i]["importe"]);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaF, $data[$i]["importe_hijoprofesor"]);
                    $devengamiento = $data[$i]["xdevengamiento_automatico"];
                    if($devengamiento==1)
                        $objPHPExcel->getActiveSheet()->setCellValue($columnaG, "SI");
                    else
                       $objPHPExcel->getActiveSheet()->setCellValue($columnaG, "NO");
                    
                    $activo = $data[$i]["activo"];
                    if($activo==1)
                        $objPHPExcel->getActiveSheet()->setCellValue($columnaH, "SI");
                    else
                       $objPHPExcel->getActiveSheet()->setCellValue($columnaH, "NO");
                    
                    $i = $i + 1;
                    $letrafilainicio += 1;
                }  
                
                $carp_cont = Yii::getAlias('@archivos'); 
                $nombre_archivo = "listadoSO" . Yii::$app->user->id . ".xlsx";                                
                $ruta_archivo = $carp_cont . "/" . $nombre_archivo;

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);                
                $writer->save($ruta_archivo);    
             
                $url_pdf = \yii\helpers\Url::to(['down-padron-excel', 'archivo' => $nombre_archivo]);     
                return $this->redirect($url_pdf);                 
            }else{
                Yii::$app->session->setFlash('error', 'Listado Vacio');
            }
        
        }catch (\Exception $e) {            
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error', Yii::$app->params['errorExcepcion']);
            return $this->redirect(['admin']);            
        }  
    }

   
    
    
}