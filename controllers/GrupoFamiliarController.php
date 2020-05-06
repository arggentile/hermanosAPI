<?php

namespace app\controllers;

use Yii;
use app\models\GrupoFamiliar;
use app\models\search\GrupoFamiliarSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

use app\models\Persona;
use app\models\Responsable;
use app\models\search\ResponsableSearch;
use app\models\Alumno;
use app\models\search\PersonaSearch;
use app\models\search\ServicioAlumnoSearch;

use app\helpers\GralException;

/**
 * GrupoFamiliarController implements the CRUD actions for GrupoFamiliar model.
 */
class GrupoFamiliarController extends Controller
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
                        'actions' => ['listado'],
                        'allow' => true,
                        'roles' => ['listarFamilias'],
                    ],
                    [     
                        'actions' => ['view','servicios-familia','action-editar-serivio-alumno','down-listado-servicios-alumno'],
                        'allow' => true,
                        'roles' => ['visualizarFamilia'],
                    ],
                    [     
                        'actions' => ['alta','actualizar'],
                        'allow' => true,
                        'roles' => ['cargarFamilia'],
                    ],
                    [     
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['eliminarFamilia'],
                    ],
                    [     
                        'actions' => ['down-padron-excel','exportar-excel','down-padron'],
                        'allow' => true,
                        'roles' => ['exportarFamilia'],
                    ],
                    [     
                        'actions' => ['asignar-responsable','carga-responsable','actualizar-responsable','quitar-responsable'],
                        'allow' => true,
                        'roles' => ['gestionarResponable'],
                    ],
                ],
                'denyCallback' => function($rule, $action){ 
                    if(Yii::$app->request->isAjax)
                        throw new GralException('Acceso denegado, usted no dispone de los permisos suficienes para realizar la accion');
                    else
                        throw new \yii\web\ForbiddenHttpException();  
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
    
    public function actions()
    {
        return [
            //accion comun para descragar archivos excel
            'down-padron-excel'=>'app\actions\DescargaPadronExcelAction', 
        ];
    } 
    
    /********************************************************************/
    /********************************************************************/
    /**
     * Lists all GrupoFamiliar models.
     * @return mixed
     */
    public function actionListado()
    {
        try{
            $export = Yii::$app->request->get('export');
            if(isset($export) && $export==1)
                return $this->exportarListado();
            
            $searchModel = new GrupoFamiliarSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }catch (\Exception $e) {            
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
        }   
        
        return $this->render('listado', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**********************************************************************/
    /**
     * Creates a new GrupoFamiliar model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAlta()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $model = new GrupoFamiliar();            
            if ($model->load(Yii::$app->request->post())){
                $response = Yii::$app->serviceGrupoFamiliar->cargarFamilia($model);
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
            //Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);
        }
        
        return $this->render('create', [
            'model' => $model,
        ]);   
    }

    /**********************************************************************/
    /**
     * Updates an existing GrupoFamiliar model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionActualizar($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $model = $this->findModel($id);
            
            if ($model->load(Yii::$app->request->post())) {
                $response = Yii::$app->serviceGrupoFamiliar->actualizarFamilia($id, $model);
                if($response['success']){
                    Yii::$app->session->setFlash('success',Yii::$app->params['cargaCorrecta']);
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                }else
                    $model->addErrors($response['error_models']);
            }                
        }catch (\Exception $e) {    
           (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
           \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
           //Yii::$app->session->setFlash('error', Yii::$app->params['operacionFallida']);
        }
        
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    
    /**********************************************************************/
    /**********************************************************************/
    /**
     * Deletes an existing GrupoFamiliar model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $response = Yii::$app->serviceGrupoFamiliar->eliminarFamilia($id);
            if($response['success']){   
                $transaction->commit();
                Yii::$app->session->setFlash('success',Yii::$app->params['eliminacionCorrecta']);
                return $this->redirect(['listado']);    
            }else{
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::$app->params['eliminacionErronea']);
                return $this->redirect(['view', 'id'=>$id]);
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
    
    /**********************************************************************/
    /**********************************************************************/
    /**
     * Displays a single GrupoFamiliar model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        try{
            $model = $this->findModel($id);

            //buscamos los responsables del grupo familiar para mostrarlos
            $queryResponsable = Responsable::find()->joinWith(['persona p']);
            $queryResponsable->andFilterWhere(['id_grupofamiliar' => $model->id,]);              
            $dataProviderResponsables = new ActiveDataProvider([
                'query' => $queryResponsable,
            ]);        
            //buscamos los alumnos del grupo familiar para mostrarlos
            $queryAlumnos = Alumno::find()->joinWith(['persona p']);
            $queryAlumnos->andFilterWhere(['id_grupofamiliar' => $model->id,]); 
            $dataProviderAlumnos = new ActiveDataProvider([
                'query' => $queryAlumnos,
            ]);        
            
        }catch (\Exception $e) {    
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error', 'No se puede visualizar los datos de la Familia debido a un error.');
            return $this->redirect(['listado']);          
        }   
        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProviderResponsables' => $dataProviderResponsables,
            'dataProviderAlumnos' => $dataProviderAlumnos,
        ]);
    }

    /**********************************************************************/
    /**
     * Finds the GrupoFamiliar model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GrupoFamiliar the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GrupoFamiliar::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }    
    
    /**********************************************************************/
    /**********************************************************************/
    /************************ Responsables ********************************/
    /*
     * Renderiza una tabla conteniendo un conjunto de registros para los
     * responsables cargados en el sistema; permitiendo la seleccion de uno de ellos 
     * para poder asignarlos a un determinado Grupo Familiar.
     * 
     * @params familia       // id del grupo familiar
     * @params responsable   // id del responsbale asignar al grupo familiar
     * @params tipores       
     */
    public function actionAsignarResponsable(){
        try{
            $familia =  Yii::$app->request->get('familia');
            $idresponsable = Yii::$app->request->get('idresponsable');
            $tiporesponsable = Yii::$app->request->get('tipores');
            $responsable = Yii::$app->request->get('responsable');
            
            $modelFamilia = GrupoFamiliar::findOne($familia);        
            if(!$modelFamilia)        
                throw  new GralException('Grupo Familiar inexistente.');  
            
            $searchModel = new PersonaSearch();         
            $searchModel->load(Yii::$app->request->get());    
            
            $queryPersonas = Persona::find();
            $queryPersonas->joinWith(['alumnos a']);            
            $dataProvider = new ActiveDataProvider([
                'query' => $queryPersonas,
                'pagination' =>[
                    'pageSize' => 5
                ]
            ]);            
            $queryPersonas->andFilterWhere(['like', 'apellido', $searchModel->apellido])
                ->andFilterWhere(['like', 'nombre', $searchModel->nombre])
                ->andFilterWhere(['like', 'nro_documento', $searchModel->nro_documento]);

            $queryPersonas->andWhere(['is','a.id', null]);

                
            if ( !empty($familia) && !empty($tiporesponsable) &&
                    !empty($idresponsable) ){
                
                $transaction = Yii::$app->db->beginTransaction(); 
                $response = Yii::$app->serviceGrupoFamiliar->asignarResponsableFamilia($idresponsable, $familia, $tiporesponsable, $responsable);
                if ($response['success']){
                    $transaction->commit();
                    Yii::$app->response->format = 'json';
                    return ['error' => '0','carga' => '1']; 
                }else{
                    $transaction->rollBack();
                    Yii::$app->response->format = 'json';
                    return ['error' => '1','carga' => '0','mensaje'=>'no se pudo realiza la asignacion del responsable']; 
                }
            }
        }catch(GralException $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));   
            throw new GralException($e->getMessage());            
        }catch(\Exception $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));                 
            throw new GralException('Error al querer asignar responsables. Intente nuevamente y en caso de persistir el error comuniquese con su administrador.');
        }          
        
        Yii::$app->response->format = 'json';
        return ['error' => '0',
            'vista' => $this->renderAjax('_asignarResponsable', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'familia' => $familia
        ])];
              
    } //fin AsignarrResponsable
    
    public function actionCargaResponsable() 
    {
        $transaction = Yii::$app->db->beginTransaction();        
        try {
            $idFamilia = Yii::$app->request->get('idFamilia');            
            $modelGrupoFamiliar = GrupoFamiliar::findOne($idFamilia);
            if (empty($modelGrupoFamiliar))
                throw new GralException('No se puede realizar la operación de crga de Responsable debido a que el identificador de '
                        . 'familia proporcionado no existe.'); 
            
            $modelResponsable = new Responsable();
            $modelResponsable->id_grupofamiliar = $idFamilia;
            $modelPersona = new Persona();

            if ($modelResponsable->load(Yii::$app->request->post()) &&
                $modelPersona->load(Yii::$app->request->post())) {
                
                $response = Yii::$app->serviceGrupoFamiliar->cargarResponsable($modelResponsable, $modelPersona);
                if ($response['success']){
                    $transaction->commit();                            
                    Yii::$app->response->format = 'json';
                    return ['error' => '0', 'carga' => '1', 'message' => Yii::$app->params['cargaCorrecta']];
                }else{
                    $transaction->rollBack();
                    Yii::$app->response->format = 'json';
                    $modelResponsable->addErrors($response['error_modelsResponsable']);
                    $modelPersona->addErrors($response['error_modelsPersona']);
                    
                    return ['error' => '0', 'carga' => '0', 'message' => Yii::$app->params['operacionFallida'],
                        'vista' => $this->renderAjax('_cargaResponsable', [
                            'model' => $modelPersona,
                            'modelResponsable' => $modelResponsable,
                    ])];
                }
            }
        }catch(GralException $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));   
            throw new GralException($e->getMessage());            
        }catch(\Exception $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));                 
            throw new GralException('Error al querer asignar responsables. Intente nuevamente y en caso de persistir el error comuniquese con su administrador.');
        }
        
        Yii::$app->response->format = 'json';
        return ['error' => '0',
                'vista' => $this->renderAjax('_cargaResponsable', [
                'model' => $modelPersona,
                'modelResponsable' => $modelResponsable,
        ])];
    } //fin CargarResponsable
   
    public function  actionActualizarResponsable()
    {              
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $idResponsable = Yii::$app->request->get('id');            
            $modelResponsable = Responsable::findOne($idResponsable);
            if (!$modelResponsable)
                throw new GralException('Responsable inexistente.');
             
            $modelPersona =  Persona::findOne($modelResponsable->id_persona);
           
            if ($modelResponsable->load(Yii::$app->request->post()) &&
                $modelPersona->load(Yii::$app->request->post())) {
                
                $response = Yii::$app->serviceGrupoFamiliar->actualizarResponsable($idResponsable,$modelResponsable, $modelPersona);
                if ($response['success']){
                    $transaction->commit();                            
                    Yii::$app->response->format = 'json';
                    return ['error' => '0', 'carga' => '1', 'message' => Yii::$app->params['actualizacionCorrecta']];
                }else{
                    $transaction->rollBack();
                    Yii::$app->response->format = 'json';
                    $modelResponsable->addErrors($response['error_modelsResponsable']);
                    $modelPersona->addErrors($response['error_modelsPersona']);
                    
                    return ['error' => '0', 'carga' => '0', 'message' => Yii::$app->params['operacionFallida'],
                        'vista' => $this->renderAjax('_cargaResponsable', [
                            'model' => $modelPersona,
                            'modelResponsable' => $modelResponsable,
                    ])];
                }
            } 
        }catch(GralException $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));   
            throw new GralException($e->getMessage());            
        }catch(\Exception $e){
           (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));             
            throw new NotFoundHttpException('El Responsbale que intenta actualizar los datos no existe.');
        }     
        
        //renderizamos las vistas, formulario de carga
        Yii::$app->response->format = 'json';
        return ['error' => '0',
                'vista' => $this->renderAjax('_cargaResponsable', [
                    'model' => $modelPersona,
                    'modelResponsable' => $modelResponsable,
                ])];
    } //fin createAjax

    /*
     * Quita un determinado registro responsbale del asociado al grupo familiar;
     * no elimina ningun dato ni regstro propio de la persona
     */
    public function  actionQuitarResponsable()
    {   
        $transaction = Yii::$app->db->beginTransaction();  
        try{
            $id = Yii::$app->request->get('id');
            $response = Yii::$app->serviceGrupoFamiliar->eliminarResponsable($id);
            if($response['success']){            
                $transaction->commit();
                Yii::$app->response->format = 'json';
                return ['error' => '0', 'mensaje' => Yii::$app->params['eliminacionCorrecta']];  
            }else{
                $transaction->rollBack();
                Yii::$app->response->format = 'json';
                return ['error' => '1', 'mensaje' => Yii::$app->params['eliminacionErronea']];  
            }
        }catch(GralException $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));   
            throw new GralException($e->getMessage());            
        }catch(\Exception $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));                 
            throw new GralException('Error al querer remover al responsable. Intente nuevamente y en caso de persistir el error comuniquese con su administrador.');
        }  
    } //fin Quitar Responsable
    
    
    /**********************************************************************/
     /**********************************************************************/
    public function actionServiciosFamilia(){        
        try{
            $idGrupoFamiliar = Yii::$app->request->get('familia');
            $modelGrupoFamiliar = $this->findModel($idGrupoFamiliar);
            if (!$modelGrupoFamiliar)
                throw new GralException('Grupo Familiar inexistente.'); 
            
            $searchModel = new ServicioAlumnoSearch();
            $searchModel->familia = $idGrupoFamiliar;
            $dataProvider = $searchModel->search(Yii::$app->request->get()); 
        }catch (GralException $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            Yii::$app->session->setFlash('error', 'No se puede visualizar los datos de la Familia debido a un error.');
            return $this->redirect(['view','id'=>$modelFamilia->id]);   
        }catch (\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            Yii::$app->session->setFlash('error', 'No se puede visualizar los datos de la Familia debido a un error.');
            return $this->redirect(['view','id'=>$modelFamilia->id]);   
        }     
        return $this->render('serviciosFamiliar', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$modelGrupoFamiliar
        ]);    
    }    
    
    /*******************************************************************/
    /******************** EXPORTACION A EXCEL **************************/            
    public function cellColor($objPHPExcel,$cells,$color)
    {
        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array('type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'startcolor' => array('rgb' => $color) ));
    }  
    
    public function exportarListado() {
        try{       
            $searchModel = new GrupoFamiliarSearch();
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
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setWrapText(true);

            
                $this->cellColor($objPHPExcel, 'A1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'B1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'C1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'D1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'E1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'F1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'G1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'H1', 'F28A8C');
                $objPHPExcel->getActiveSheet()->setCellValue('A1', 'APELLIDOS');
                $objPHPExcel->getActiveSheet()->setCellValue('C1', 'DESCRIPCION');                
                $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Nº Hijos');
                $objPHPExcel->getActiveSheet()->setCellValue('E1', 'HIJOS');
                $objPHPExcel->getActiveSheet()->setCellValue('F1', 'PAGO ASOCIADO');
                $objPHPExcel->getActiveSheet()->setCellValue('G1', 'TC/CBU');
             
                $objPHPExcel->getActiveSheet()->setCellValue('H1', 'CUIL Fact.AFIP');
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
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaA, $data[$i]["apellidos"]);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaB, $data[$i]["folio"]);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaC, $data[$i]["descripcion"]);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaD, $data[$i]["cantidadHijos"] );
                   
                    $familia = GrupoFamiliar::findOne($data[$i]['id']);
                    $misHijos = $familia->alumnos;
                    
                    $alumnos = "";
                    if($misHijos)
                        foreach ($misHijos as $key => $hijo){
                        $alumnos .= $hijo->persona->nro_documento." ".$hijo->persona->apellido. ", ".$hijo->persona->nombre;
                        if($key + 1 !== count($misHijos))
                            $alumnos .= " \n ";
                        }
                    $objPHPExcel->getActiveSheet()->getStyle($columnaE)->getAlignment()->setWrapText(true);
                    
                    
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaE, $alumnos);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaF, $data[$i]["pagoAsociado"]["nombre"]);
                    
                    if($data[$i]["id_pago_asociado"]=='4')
                        $objPHPExcel->getActiveSheet()->setCellValue($columnaG, $data[$i]["cbu_cuenta"]);
                    elseif($data[$i]["id_pago_asociado"]=='5')
                        $objPHPExcel->getActiveSheet()->setCellValue($columnaG, $data[$i]["nro_tarjetacredito"]);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaH, $data[$i]["cuil_afip_pago"] );
                             
                    $i = $i + 1;
                    $letrafilainicio += 1;
                }  
                
                $carp_cont = Yii::getAlias('@webroot') . "/archivos_generados"; //carpeta a almacenar los archivos
                $nombre_archivo = "listadoFamilias" . Yii::$app->user->id . ".xlsx";                                
                $ruta_archivo = $carp_cont . "/" . $nombre_archivo;
            
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
                $writer->save($ruta_archivo);
                $url_pdf = \yii\helpers\Url::to(['down-padron-excel', 'archivo' => $nombre_archivo]);
                return $this->redirect($url_pdf);
                
            }else{                
                Yii::$app->session->setFlash('error', 'Listado Vacio.');
            }
        
        }catch (\Exception $e) {            
            Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
            Yii::$app->session->setFlash('error', Yii::$app->params['errorExcepcion']);
            return $this->redirect(['site/index']);            
        }  
    }
  
         
}
