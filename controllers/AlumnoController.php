<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

use app\models\Alumno;
use app\models\search\AlumnoSearch;
use app\models\Persona;
use app\models\GrupoFamiliar;
use app\models\forms\EgresoAlumnoForm;

use app\helpers\GralException;
use yii\web\HttpException;
/**
 * AlumnoController implements the CRUD actions for Alumno model.
 */
class AlumnoController extends Controller
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
                            'roles' => ['listarAlumnos'],
                    ],
                    [     
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['eliminarAlumno'],
                    ],
                    [     
                        'actions' => ['empadronamiento','buscarFamilia','mis-divisionesescolares'],
                        'allow' => true,
                        'roles' => ['cargarAlumno'],
                    ],
                    [     
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['visualizarAlumno'],
                    ],
                    [     
                        'actions' => ['activar'],
                        'allow' => true,
                        'roles' => ['activarAlumno'],
                    ],
                    [     
                        'actions' => ['inactivar'],
                        'allow' => true,
                        'roles' => ['inactivarAlumno'],
                    ],                    
                    [     
                        'actions' => ['down-padron-excel','exportar-excel','down-listado-servicios-alumno'],
                        'allow' => true,
                        'roles' => ['exportarAlumno'],
                    ],
                    [     
                        'actions' => ['asignar-bonificacion','quitar-bonificacion','eliminar-bonificacion','bonificaciones-alumno'],
                        'allow' => true,
                        'roles' => ['gestionarBonificacionAlumno'],
                    ],
                    [     
                        'actions' => ['mis-divisionesescolares','hola'],
                        'allow' => true,                       
                    ],
                    [     
                        'actions' => ['egresar-alumnos'],
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
    }
    

    public function actions()
    {
        return [           
            'buscarFamilia' => 'app\actions\BuscarFamiliaAction',
            //accion comun para descargar archivos excel
            'down-padron-excel'=>'app\actions\DescargaPadronExcelAction',            
        ];
    } 
    
    /***************************************************************/
    /***************************************************************/
    /**
     * Finds the Alumno model based on its primary key value.
    }
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Alumno the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Alumno::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /***************************************************************/
    /***************************************************************/
    /**
     * Lists all Alumno models.
     * @return mixed
     */
    public function actionListado()
    {
        try{
            $export = Yii::$app->request->get('export');
            if(isset($export) && $export==1)
                return $this->exportarListado();
            
            $searchModelPersona =  new \app\models\search\PersonaSearch();
            $searchModelPersona->load(Yii::$app->request->queryParams);
            
            $searchModel = new AlumnoSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $searchModelPersona);             
           
            $data = [];
            $data['filtros'] = [];
            $data['filtros']['dropEstablecimientosSearch'] = \app\models\Establecimiento::getEstablecimientos();
            //$data['filtros']['dropDivisionesSearch'] = \app\models\DivisionEscolar::getDivisionesEscolares();
            $data['filtros']['dropDivisionesSearch'] = [];
            $data['filtros']['sino'] = [ '0'=>'No', '1' => 'Si'];
        }catch (\Exception $e) {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error', Yii::$app->params['errorExcepcion']);                      
        }
        return $this->render('listado', [
            'searchModel' => $searchModel,
            'data'=>$data,
            'dataProvider' => $dataProvider,
            'searchModelPersona'=> $searchModelPersona,
        ]);
    }

    /*******************************************************************/
    /********************** Eliminacion ********************************/ 
    /**
     * Deletes an existing Alumno model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    { 
        try{
            $transaction = Yii::$app->db->beginTransaction(); 
            $response = Yii::$app->serviceAlumno->eliminarAlumno($id);
            if($response['success']){  
                $transaction->commit();
                Yii::$app->session->setFlash('success',Yii::$app->params['eliminacionCorrecta']);
                return $this->redirect(['listado']);    
            }else{
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::$app->params['eliminacionErronea']);
                return $this->redirect(['view','id'=>$id]);
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
     
    public function actionActivar($id)
    {
        try{
            $transaction = Yii::$app->db->beginTransaction(); 
            $response = Yii::$app->serviceAlumno->activarAlumno($id);            
            if($response['success']){
                $transaction->commit();                                    
                Yii::$app->response->format = 'json';
                return ['error' => '0', 'mensaje' => 'Se activo Correctamente!!!'];                
            }
            else{
                $transaction->rollBack();                                  
                Yii::$app->response->format = 'json';
                return ['error' => '1', 'mensaje' => 'Error al activar al Alumno!!!'];
            }
        }catch(GralException $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            
           throw new  GralException('Error severo al querer activar al alumno');        
           exit;
        }catch(\Exception $e) {     
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw  new HttpException(500, 'Error severo al querer activar al alumno');
        }  catch(T $e) {
    echo $e->getMessage();exit;
}     
    }   
    
    public function actionInactivar($id)
    {
        try{
            $transaction = Yii::$app->db->beginTransaction(); 
            $response = Yii::$app->serviceAlumno->inactivarAlumno($id);            
            if($response['success']){
                $transaction->commit();                                    
                Yii::$app->response->format = 'json';
                return ['error' => '0', 'mensaje' => 'Se Inactivo Correctamente!!!'];                
            }
            else{
                $transaction->rollBack();                                  
                Yii::$app->response->format = 'json';
                return ['error' => '1', 'mensaje' => 'Error al inactivar al Alumno!!!'];
            }
        }catch (GralException $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            throw  yii\web\HttpException(500, 'Error severo al querer inactivar al alumno');           
        }catch (\Exception $e) {            
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw  new HttpException(500, 'Error severo al querer inactivar al alumno');
        }       
    }   
    
    /***************************************************************/
    /**************************************************************/
    /**
     * Creates a new Alumno model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionEmpadronamiento($id=null)
    {   
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $id = Yii::$app->request->get('id');
            $familia = Yii::$app->request->get('familia');
            
            //si venimos de una familia bloqueamos el buscador
            $bloquearFamilia = false;
            //modo actualización
            if(!empty($id)){
                $modelAlumno = $this->findModel($id);                
                
                $modelPersona = Persona::findOne($modelAlumno->id_persona);
                $modelGrupoFamiliar = GrupoFamiliar::findOne($modelAlumno->id_grupofamiliar);
                $modelAlumno->establecimiento = \app\models\DivisionEscolar::findOne($modelAlumno->id_divisionescolar)->id_establecimiento;
            }
            else{    
                $modelPersona = new Persona();
                $modelAlumno = new Alumno();
                $modelAlumno->activo = '1';
                $modelAlumno->egresado = '0';                
                $modelGrupoFamiliar = \app\models\GrupoFamiliar::findOne($familia);
                if(empty($modelGrupoFamiliar)){
                    $modelGrupoFamiliar = new GrupoFamiliar();
                    $bloquearFamilia = false;
                }
                else{
                    $modelAlumno->id_grupofamiliar = $modelGrupoFamiliar->id;     
                    $bloquearFamilia = true;
                }
            }           
            
            if(!empty(Yii::$app->request->post('mifamilia')))
                $modelGrupoFamiliar = GrupoFamiliar::findOne(Yii::$app->request->post('mifamilia'));
            
            $data = [];
            
            if($modelAlumno->load(Yii::$app->request->post()) && $modelPersona->load(Yii::$app->request->post())){                                
                if(!$modelGrupoFamiliar){                    
                    $modelGrupoFamiliar = new GrupoFamiliar();
                }else{                  
                    $modelAlumno->id_grupofamiliar = $modelGrupoFamiliar->id;      
                    if(!empty($id))
                        $response = Yii::$app->serviceAlumno->actualizarAlumno($id, $modelAlumno, $modelPersona);   
                    else
                        $response = Yii::$app->serviceAlumno->cargarAlumno($modelAlumno, $modelPersona); 
                    
                    if($response['success']){
                        $idAlumno = $response['nuevoModeloAlumno']->id;
                        //\Yii::$app->getModule('audit')->data('sss', \yii\helpers\VarDumper::dumpAsString($response));
                        Yii::$app->session->setFlash('success',Yii::$app->params['cargaCorrecta']);
                        $transaction->commit();
                        //return $this->redirect(\yii\helpers\Url::to(['/alumno/view', 'id' => $idAlumno]));
                        return $this->redirect(['view', 'id' => $response['nuevoModeloAlumno']->id ]);
                    }else{
                        $modelPersona->addErrors($response['error_modelPersona']);
                        $modelAlumno->addErrors($response['error_modelAlumno']);                      
                    }
                }
            } 
            
            $data['filtros'] = [];
            $data['filtros']['dropEstablecimientosSearch'] = \app\models\Establecimiento::getEstablecimientos();
           
            if(!empty($modelAlumno->establecimiento)){
                $divisiones = \app\models\DivisionEscolar::find()->joinWith('establecimiento e')->where(['=', 'e.id', $modelAlumno->establecimiento])->asArray()->all();
                $divisiones = yii\helpers\ArrayHelper::map($divisiones, 'id', 'nombre');
            }else
                $divisiones = array();
            $data['filtros']['dropDivisionesSearch'] = $divisiones;
            $data['filtros']['sino'] = [ '0'=>'No', '1' => 'Si'];
            
        }catch (\Exception $e){
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error',Yii::$app->params['operacionFallida']);             
        }  
        
        return $this->render('create', [
            'modelAlumno' => $modelAlumno,
            'modelPersona' => $modelPersona,
            'modelGrupoFamiliar' =>  $modelGrupoFamiliar,
            'data'=>$data,
            'bloquearFamilia' => $bloquearFamilia
        ]);
    }
    
    /***************************************************************/
    /**************************************************************/
    /**
     * Displays a single Alumno model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        try{
            $misBonificaciones = new \yii\data\ActiveDataProvider([
                    'query' => \app\models\BonificacionAlumno::find()->where('id_alumno='.$id),
                ]);        

            $searchMisServicios = new \app\models\search\ServicioAlumnoSearch(); 
            $searchMisServicios->id_alumno = $id;        
            $misServicios = $searchMisServicios->search(Yii::$app->request->queryParams);  
            
            return $this->render('view', [
                'model' => $this->findModel($id),
                'misBonificaciones' => $misBonificaciones,
                'misServicios' => $misServicios,
                'searchMisServicios'=>$searchMisServicios
            ]);        
            
        }catch (\Exception $e) {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));           
            Yii::$app->session->setFlash('error', Yii::$app->params['errorExcepcion']);
            return $this->redirect(['site/index']);            
        }  
       
        
    }    
    
    /**********************************************************************/
    /************************ Bonificaciones ******************************/
    public function actionAsignarBonificacion($alumno){        
        try{
            $transaction = Yii::$app->db->beginTransaction(); 
                
            $model = new \app\models\BonificacionAlumno();
            $model->id_alumno = $alumno;        

            if ($model->load(Yii::$app->request->post())){
                $response = Yii::$app->serviceAlumno->asignarBonificacion($alumno, $model);            
                if($response['success']){
                    $transaction->commit();                                    
                    Yii::$app->response->format = 'json';
                    return ['error' => '0', 'carga' => '1','mensaje' => 'Se realizó la asignasión de la bonificación.'];          
                }
                else{
                    $transaction->rollBack();                                  
                    Yii::$app->response->format = 'json';
                    return ['error' => '0', 'carga' => '0','mensaje' => 'dfg',
                        'vista' => $this->renderAjax('_formAsignacionBonificacion', [
                                        'model' => $model,
                                     ])];
                }
            }
        }catch (GralException $e) {
            (isset($transaction) && $transaction->isActive)?$transaction->rollBack():'';
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));  
            throw new GralException($e->getMessage());            
        }catch(\Exception $e){ 
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw new HttpException(500, 'Error severo al querer asignar la bonificación  al alumno');
        }
        
        Yii::$app->response->format = 'json';
        return ['error' => '0',
            'vista' => $this->renderAjax('_formAsignacionBonificacion', [
                            'model' => $model,
                         ])];
        
        
    }    
    
    public function actionQuitarBonificacion($id)
    {        
        try{
            $transaction = Yii::$app->db->beginTransaction(); 
            $response = Yii::$app->serviceAlumno->eliminarBonificacion($id);  
            
            if ($response['success'] ){
                $transaction->commit();
                if (Yii::$app->request->isAjax){                    
                    Yii::$app->response->format = 'json';
                    return ['error' => '0', 'mensaje' => Yii::$app->params['eliminacionCorrecta']];
                }
            }
        }catch (\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
            $transaction->rollBack();
            if (Yii::$app->request->isAjax){
                Yii::$app->response->format = 'json';
                return ['error' => '1', 'mensaje' =>  Yii::$app->params['errorExcepcion']];
            }
        }
    }  

    public function actionBonificacionesAlumno(){   
        try{
            $export = Yii::$app->request->get('export');
            if(isset($export) && $export==1)
                return $this->exportarListadoBonificaciones();
            
            $modelPersona =  new \app\models\search\PersonaSearch();
            $modelPersona->load(Yii::$app->request->queryParams);
        
            $searchModelBonificacion = new \app\models\search\BonificacionAlumnoSearch();
            $dataProviderBonificacion = $searchModelBonificacion->search(Yii::$app->request->queryParams, $modelPersona);
        }catch (\Exception $e) {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
            Yii::$app->session->setFlash('error', Yii::$app->params['errorExcepcion']);                      
        }   
        
        return $this->render('reporteBonificaciones/bonificacionesalumno',[
            'searchModel' => $searchModelBonificacion,
            'dataProvider' => $dataProviderBonificacion,
            'modelPersona'=> $modelPersona,
        ]);
    }
    
    /*******************************************************************/
    /******************** exportacion a excel **************************/            
    public function cellColor($objPHPExcel,$cells,$color)
    {
        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array('type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'startcolor' => array('rgb' => $color) ));
    }  
    
    public function exportarListado() 
    {  
        ini_set('memory_limit', '-1');
        ini_set('set_time_limite', '900');
        ini_set('max_execution_time', 900);   
        try{
            
            $searchModelPersona =  new \app\models\search\PersonaSearch();
            $searchModelPersona->load(Yii::$app->request->queryParams);
            
            $searchModel = new AlumnoSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $searchModelPersona); 
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
                $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);

                $this->cellColor($objPHPExcel, 'A1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'B1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'C1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'D1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'E1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'F1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'G1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'H1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'I1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'J1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'K1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'L1', 'F28A8C');
                $this->cellColor($objPHPExcel, 'M1', 'F28A8C');

                $objPHPExcel->getActiveSheet()->setCellValue('A1', 'DNI');
                $objPHPExcel->getActiveSheet()->setCellValue('B1', 'APELLIDO');
                $objPHPExcel->getActiveSheet()->setCellValue('C1', 'NOMBRE');
                $objPHPExcel->getActiveSheet()->setCellValue('D1', 'FECHA NACIMIENTO');
                $objPHPExcel->getActiveSheet()->setCellValue('E1', 'DOMICILIO');
                $objPHPExcel->getActiveSheet()->setCellValue('F1', 'TELEFONO');
                $objPHPExcel->getActiveSheet()->setCellValue('G1', 'NRO LEGAJO');
                $objPHPExcel->getActiveSheet()->setCellValue('H1', 'ACTIVO');
                $objPHPExcel->getActiveSheet()->setCellValue('I1', 'ESTABLECIMIENTO');
                $objPHPExcel->getActiveSheet()->setCellValue('J1', 'DIVISION');
                $objPHPExcel->getActiveSheet()->setCellValue('K1', 'BONIFICACION');
                $objPHPExcel->getActiveSheet()->setCellValue('L1', 'FAMILIA');
                $objPHPExcel->getActiveSheet()->setCellValue('M1', 'HIJO PROFESOR');
                
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
                    $columnaI = 'I' . $letrafilainicio1;
                    $columnaJ = 'J' . $letrafilainicio1;
                    $columnaK = 'K' . $letrafilainicio1;
                    $columnaL = 'L' . $letrafilainicio1;
                    $columnaM = 'M' . $letrafilainicio1;

                    $objPHPExcel->getActiveSheet()->setCellValue($columnaA, $data[$i]["persona"]["nro_documento"]);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaB, $data[$i]["persona"]["apellido"]);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaC, $data[$i]["persona"]["nombre"]);
                    $fecha_nacimiento = \app\models\Fecha::convertirFecha($data[$i]["persona"]["fecha_nacimiento"], 'Y-m-d','d-m-Y');
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaD, $fecha_nacimiento );
                    
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaE, $data[$i]["persona"]["miDomicilio"]);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaF, $data[$i]["persona"]["miTelContacto"]);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaG, $data[$i]["nro_legajo"]);
                
                    if ($data[$i]["activo"] == '1') {
                        //$this->cellColor($objPHPExcel, $columnaH, '89d4a3');
                        $objPHPExcel->getActiveSheet()->setCellValue($columnaH, "SI");
                    } else {
                       // $this->cellColor($objPHPExcel, $columnaH, '319cda');
                        $objPHPExcel->getActiveSheet()->setCellValue($columnaH, "NO");
                    }
                    
                    $miDivision = \app\models\DivisionEscolar::findOne($data[$i]["id_divisionescolar"]);
                    $miEstablecimiento = \app\models\Establecimiento::findOne($miDivision->id_establecimiento);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaI, $miEstablecimiento->nombre);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaJ, $miDivision->nombre);
                    
                    $bonificaciones = \app\models\BonificacionAlumno::find()->where('id_alumno ='.$data[$i]["id"])->all();
                    $textoBonificaciones ='';
                    $lfcr = chr(10) . chr(13);
                    if(count($bonificaciones)>0){
                        foreach($bonificaciones as $one)
                            $textoBonificaciones.="\n" . $one->bonificacion->descripcion;
                    }
                    
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaK, $textoBonificaciones);
                    
                    //$objPHPExcel->getActiveSheet()->setCellValue($columnaK, 'colocar texto de la bonificacion');
                    
                    $familia = \app\models\GrupoFamiliar::findOne($data[$i]["id_grupofamiliar"]);                    
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaL, $familia->apellidos . " - Folio: ".$familia->folio);
                    if ($data[$i]["hijo_profesor"] == '1') {                        
                        $objPHPExcel->getActiveSheet()->setCellValue($columnaM, "SI");
                    } else {                       
                        $objPHPExcel->getActiveSheet()->setCellValue($columnaM, "NO");
                    }

                    $i = $i + 1;
                    $letrafilainicio += 1;
                }  

                $carp_cont = Yii::getAlias('@archivos'); 
                $nombre_archivo = "listadoAlumnos" . Yii::$app->user->id . ".xlsx";                                
                $ruta_archivo = $carp_cont . "/" . $nombre_archivo;

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);                
                $writer->save($ruta_archivo);                    
                $url_pdf = \yii\helpers\Url::to(['down-padron-excel', 'archivo' => $nombre_archivo]);               
                return $this->redirect($url_pdf); 
            }else{                           
                Yii::$app->session->setFlash('error', 'Listado Vacio.');
            }        
        }catch (\Exception $e) {           
           
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
            Yii::$app->session->setFlash('error', Yii::$app->params['errorExcepcion']);
            return $this->redirect(['/site/index']);            
        }  
    }
    
    /********************************************************/
    public function exportarListadoBonificaciones() { 
        try{
            $modelPersona =  new \app\models\search\PersonaSearch();
            $modelPersona->load(Yii::$app->request->queryParams);
        
            $searchModelBonificacion = new \app\models\search\BonificacionAlumnoSearch();
            $dataProviderBonificacion = $searchModelBonificacion->search(Yii::$app->request->queryParams, $modelPersona);
            $dataProviderBonificacion->setPagination(false);        
      
            $data = $dataProviderBonificacion->getModels();          
            
            $i = 0;                        
            $contador = count($data);
           
            $objPHPExcel = new Spreadsheet(); 
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);


            $this->cellColor($objPHPExcel, 'A1', 'F28A8C');
            $this->cellColor($objPHPExcel, 'B1', 'F28A8C');
            $this->cellColor($objPHPExcel, 'C1', 'F28A8C');
            $this->cellColor($objPHPExcel, 'D1', 'F28A8C');
            $this->cellColor($objPHPExcel, 'E1', 'F28A8C');
            $this->cellColor($objPHPExcel, 'F1', 'F28A8C');
               
                
            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'DNI');
            $objPHPExcel->getActiveSheet()->setCellValue('B1', 'APELLIDO');
            $objPHPExcel->getActiveSheet()->setCellValue('C1', 'NOMBRE');
            $objPHPExcel->getActiveSheet()->setCellValue('D1', 'FAMILIA');
            $objPHPExcel->getActiveSheet()->setCellValue('E1', 'FOLIO');
            $objPHPExcel->getActiveSheet()->setCellValue('F1', 'BONIFICACION');

            $letracolumnainicio = 'A';
            $letrafilainicio = 3;

            while($i < $contador) {
                $letrafilainicio1 = (string) $letrafilainicio;
                $columnaA = 'A' . $letrafilainicio1;
                $columnaB = 'B' . $letrafilainicio1;
                $columnaC = 'C' . $letrafilainicio1;
                $columnaD = 'D' . $letrafilainicio1;
                $columnaE = 'E' . $letrafilainicio1;  
                $columnaF = 'F' . $letrafilainicio1;  

                $objPHPExcel->getActiveSheet()->setCellValue($columnaA, $data[$i]["alumno"]["persona"]["nro_documento"]);
                $objPHPExcel->getActiveSheet()->setCellValue($columnaB, $data[$i]["alumno"]["persona"]["apellido"]);
                $objPHPExcel->getActiveSheet()->setCellValue($columnaC, $data[$i]["alumno"]["persona"]["nombre"]);
                $objPHPExcel->getActiveSheet()->setCellValue($columnaD, $data[$i]["alumno"]["grupofamiliar"]["apellidos"]);
                $objPHPExcel->getActiveSheet()->setCellValue($columnaE, $data[$i]["alumno"]["grupofamiliar"]["folio"]);
                $objPHPExcel->getActiveSheet()->setCellValue($columnaF, $data[$i]["bonificacion"]["descripcion"] . " ".$data[$i]["bonificacion"]["valor"]);
                $i = $i + 1;
                $letrafilainicio += 1;
            }  

            $carp_cont = Yii::getAlias('@webroot') . "/archivos_generados"; //carpeta a almacenar los archivos
            $nombre_archivo = "listadoBonificacionesAlumnos" . Yii::$app->user->id . ".xlsx";                                
            $ruta_archivo = $carp_cont . "/" . $nombre_archivo;

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
            $writer->save($ruta_archivo); 
            $url_pdf = \yii\helpers\Url::to(['down-padron-excel', 'archivo' => $nombre_archivo]);               
            return $this->redirect($url_pdf); 
        }catch (\Exception $e) {
            Yii::error('exportar Alumnos '.$e);
            Yii::$app->session->setFlash('error', Yii::$app->params['errorExcepcion']);
            return $this->redirect(['site/index']);            
        }  
    }
    
    /*****************************************************/
    /* Renderiza un formulario para el egreso de alumno,
     * para esto selepermite seleccionar alusuario la division escolar a elegir los 
     * alumnos a egrear y a continuacion se denderiza una grilla para seleccionar 
     * los alumnos, junto con la division ah realizr el egreso
     */
    public function actionEgresarAlumnos()
    {
        $transaction = Yii::$app->db->beginTransaction(); 
        try{
            $dataProviderAlumnos = null; //provider de alumnos a egresar 
            //mantenemos los nombres para solo advertir en el alert de migracion
            $establecimientoInicial = null;
            $divisionInicial = null;
            
            $modelEgreso = new EgresoAlumnoForm();
            
            
            $alumnosSelects = Yii::$app->request->post('selection');
            if (!empty($alumnosSelects)){ 
                $modelEgreso->load(Yii::$app->request->post());       
                
                if($modelEgreso->validate()){            
                    $valid = true;  
                    $idDivionEgreso = $modelEgreso->id_divisionescolar;
                    foreach($alumnosSelects as $alumnoAEgresar){
                        if($modelEgreso->es_egreso=='0')
                            $response = Yii::$app->serviceAlumno->egresarAlumnoDivision($alumnoAEgresar,$idDivionEgreso,$modelEgreso->fecha_egreso);
                        else
                            $response = Yii::$app->serviceAlumno->egresarAlumno($alumnoAEgresar,$modelEgreso->fecha_egreso);
                        if($response['success']){  
                            $valid = true;                     
                        }else{
                            $valid = false;
                            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($response['error_models']));  
                            throw  new GralException('Error al grabar el egreso en uno de los alumnos');
                        }    
                    }
                    if($valid){
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', 'Se realizó laoperación de egreso satifactoriamente.');
                        return $this->redirect(['egresar-alumnos']); 
                    }
                }                
            }
            
            
            $id_division_aegresar = Yii::$app->request->get('id_division_ageresar');
            $id_establecimiento_aegresar = Yii::$app->request->get('id_establecimiento_ageresar');
            
            if(!empty($id_division_aegresar) && !empty($id_establecimiento_aegresar)) {
                $modelEstablecimiento = \app\models\Establecimiento::findOne($id_establecimiento_aegresar);
                if(!$modelEstablecimiento)
                    throw new GralException('Establecimiento inexistente');
                $modelDivision = \app\models\DivisionEscolar::findOne($id_division_aegresar);
                if(!$modelDivision)
                    throw new GralException('Division Escolar inexistente');
                
                //mantenemos los nombres para solo advertir en el aleert de migracion
                $establecimientoInicial = $modelEstablecimiento->nombre;
                $divisionInicial = $modelDivision->nombre;
            
                $searchModel = Alumno::find()->andWhere(['activo'=>'1'])->andWhere(['id_divisionescolar'=>$modelDivision->id]);
                $dataProviderAlumnos  = new \yii\data\ActiveDataProvider([
                                    'query' => $searchModel,   
                                    
                                ]);
                $dataProviderAlumnos->setPagination(false); 
            }
            
            
            $data['filtros'] = [];
            $data['filtros']['dropEstablecimientosSearch'] = \app\models\Establecimiento::getEstablecimientos();
            $data['filtros']['dropDivisionesSearch'] = [];
        }catch (GralException $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error',Yii::$app->params['operacionFallida']);             
        }catch (\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error',Yii::$app->params['operacionFallida']);             
        }    
        
        return $this->render('formEgresoAlumnos', [
            'modelAlumnoEgreso'=>$modelEgreso,
            'data'=>$data,
            'dataProviderAlumnos' => $dataProviderAlumnos,
            'establecimientoInicial'=>$establecimientoInicial,
            'divisionInicial'=>$divisionInicial
        ]);      
    }
    
    public function actionHola(){
//      phpinfo();
//      exit;
        try{
            $valid = true;
            for($i =1; $i <100; $i++){
                $dd = \app\models\Factura::avisarAfip(13, 1, "CUIL", "20327097351", 100, "2020-10-01");
                var_dump($dd);
            }
               \Yii::$app->getModule('audit')->data('1234', \yii\helpers\VarDumper::dumpAsString($dd));
        }catch (GralException $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error',Yii::$app->params['operacionFallida']);             
        }catch (\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error',Yii::$app->params['operacionFallida']);             
        }   
    }
    
   
    // 20073985529  Porrohugo1944   30630291727
    
 
    
  
   
}
