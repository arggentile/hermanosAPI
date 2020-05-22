<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use kartik\mpdf\Pdf;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

use app\models\ConvenioPago;
use app\models\search\ConvenioPagoSearch;
use app\models\Abogado;
use app\models\Persona;
use app\models\CuotaConvenioPago;
use app\models\ServicioConvenioPago;

use app\helpers\GralException;

/**
 * ConvenioPagoController implements the CRUD actions for ConvenioPago model.
 */
class ConvenioPagoController extends Controller
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
                        'actions' => ['administrar'],
                        'allow' => true,
                        'roles' => ['listarConveioPago'],
                    ],
                    [     
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['eliminarConvenioPago'],
                    ],
                    [     
                        'actions' => ['alta','alta-servicios','generar-plan-pago','add-cuota'],
                        'allow' => true,
                        'roles' => ['cargarConvenioPago'],
                    ],
                    [     
                        'actions' => ['editar-plan-pago','add-cuota'],
                        'allow' => true,
                        'roles' => ['editarConvenioPago'],
                    ],
                    [     
                        'actions' => ['view','enviar-correo','down-pdf','pfd'],
                        'allow' => true,
                        'roles' => ['visualizarConvenioPago',],
                    ],
                    [                        
                        'allow' => true,
                        'roles' => ['gestionarConvenioPago'],
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
                    'delete' => ['GET','POST'],
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
     * Finds the ConvenioPago model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ConvenioPago the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ConvenioPago::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    /*******************************************************************/
    /********************** Eliminacion ********************************/ 
    /**
     * Deletes an existing ConvenioPago model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    { 
        try{
            $transaction = Yii::$app->db->beginTransaction(); 
            $response = Yii::$app->serviceConvenioPago->eliminarConvenio($id);
            if($response['success']){  
                $transaction->commit();
                Yii::$app->session->setFlash('success',Yii::$app->params['eliminacionCorrecta']);
                return $this->redirect(['administrar']);    
            }else{
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::$app->params['eliminacionErronea']);
                return $this->redirect(Yii::$app->request->referrer);
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
    
    /********************************************************************/
    /********************************************************************/
    /**
     * Lists all ConvenioPago models.
     * @return mixed
     */
    public function actionAdministrar()
    {
        try{
            $export = Yii::$app->request->get('export');
            if(isset($export) && $export==1)
                return $this->exportarListado();
            
            $searchModel = new ConvenioPagoSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);            
        }catch(\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));            
            //Yii::$app->session->setFlash('error','ERROR SEVERO!!!');
        } 
        
        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);         
    }     
    
    /********************************************************************/
    /********************************************************************/
    /**
     * Displays a single ConvenioPago model.
     * @param integer $id
     * @return mixed
     */
    public function actionView(){
        try{        
            $id = Yii::$app->request->get('id');
            $modelConvenioPago = $this->findModel($id);  
        
            $queryServicios = ServicioConvenioPago::find()->andFilterWhere([           
                'id_conveniopago' => $modelConvenioPago->id,            
            ]);
            $misServicios = new ActiveDataProvider([
                'query' => $queryServicios,
            ]);

            $queryCuotas = CuotaConvenioPago::find()->andFilterWhere([           
                'id_conveniopago' => $modelConvenioPago->id,            
            ]);   
            $misCuotas = new ActiveDataProvider([
                'query' => $queryCuotas,
                'sort' => [
                    'defaultOrder' => [
                        'fecha_establecida' => SORT_ASC,
                    ]
                ],
            ]);    
        }catch (\Exception $e){ 
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error','Atención!!! Se Produjo un error severo'); 
            return $this->redirect(['administrar']);             
        }       
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'misServicios'=>$misServicios,
            'misCuotas'=>$misCuotas,            
        ]);
    }    
    
    /***********************************************************/
    /***********************************************************/ 
    /*
     * Alta ConvenioPago
     * 
     * Accion que se encarga de dar el alta de un convenio de pago, 
     * para un determindo cliente. Se apoya de un accion privada
     * que maneja el alta propiamente dicha
     * 
     * Como primer llamada renderiza un formulario, para buscar al cliente,
     * una vez seleccionado el cliente, llamamos a la funcion GenerarPLanPago
     * la que se encargara delproceso de alta.
     * 
     */
    public function actionAlta(){
        try{            
            $modelGrupoFamiliar = new \app\models\GrupoFamiliar(); 
            
            $query = \app\models\GrupoFamiliar::find()->alias('f')->select('f.*')->distinct(); 
            $query->joinWith(['responsables.persona p']);                    
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
            
            if($modelGrupoFamiliar->load(Yii::$app->request->post())){
                $query->andFilterWhere(['like', 'apellidos', $modelGrupoFamiliar->apellidos])
                      ->andFilterWhere(['like', 'folio', $modelGrupoFamiliar->folio]);
                $query->andFilterWhere(['like', 'p.apellido', $modelGrupoFamiliar->responsable]);
            }
        }catch (\Exception $e){ 
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error','Atención!!! Se Produjo un error severo');
            return $this->redirect(['administrar']); 
        }
        
        return $this->render('alta',[
            'modelGrupoFamiliar'=>$modelGrupoFamiliar, 
            'dataClientes'=>$dataProvider,   
        ]);
    } //fin actionALTA    
    


    

    /***********************************************************/ 
    /*
     * Seleccion de servicios a integrar en el convenio de pago
     */
    public function actionAltaServicios(){        
        try{
            $familia = Yii::$app->request->get('familia');        
            $modelFamilia = \app\models\GrupoFamiliar::findOne($familia);  
            if(!$modelFamilia)
                throw new GralException('Grupo Familiar inexistente, para generar el Convenio Pago.');
            
            $modelSearchServiciosImpagos = new \app\models\ServicioAlumno();
            $modelSearchServiciosImpagos->load(Yii::$app->request->get());
            
            /*
             * Establece los servicios de alumno a integrar en el convenio d epago
            */
            $servicioEnConvenio = Yii::$app->request->get('servicios');
               
            $servicios = [];
            if(!empty($servicioEnConvenio)){               
                $servicios = explode(',', $servicioEnConvenio);
            }           
           
            /*
             * Buscamos los servicios impagos del servicio
             */
            
            $serviciosImpagos  = Yii::$app->serviceServicioAlumno->getDevolverServiciosImpagosLibres($modelSearchServiciosImpagos, $familia, null, $servicios); 
          
            
            /*
             * Buscamos los servicios a adherir al convenio 
             */
            $queryServiciosAdheridos = \app\models\ServicioAlumno::find();
            $queryServiciosAdheridos->joinWith('miAlumno a');
            
            if(empty($servicios)){
                $queryServiciosAdheridos->andFilterWhere(['in', \app\models\ServicioAlumno::tableName(). '.id' ,  ['']]);
            }
            else{
                $queryServiciosAdheridos->andFilterWhere(['in', \app\models\ServicioAlumno::tableName(). '.id' ,  $servicios]); 
                $queryServiciosAdheridos->andFilterWhere(['a.id_grupofamiliar' => $modelFamilia->id]);       
            }    
            
            $serviciosAdheridos = new \yii\data\ActiveDataProvider([
                'query' => $queryServiciosAdheridos,
                'sort' => ['defaultOrder'=>'id desc'],
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
            $filterDropFamilia = \app\models\Alumno::getAlumnosFamiliaDrop($modelFamilia->id);
           
        }catch(\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            \Yii::$app->session->setFlash('error','Atención!!!  Se Produjo un error severo');
            return $this->redirect(['administrar']); 
        }   
        
        return $this->render('alta-servicios',[
            'modelFamilia'=>$modelFamilia,
            'serviciosImpagos'=>$serviciosImpagos,
            'serviciosAdheridos' => $serviciosAdheridos,
            'servicioEnConvenio'=>$servicioEnConvenio,
            'modelSearchServiciosImpagos'=>$modelSearchServiciosImpagos,
            'filterDropFamilia'=>$filterDropFamilia
        ]);  
        
    }
    
    /******************************************************/    
    public function actionGenerarPlanPago(){ 
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $familia = Yii::$app->request->get('familia');
            $modelFamilia = \app\models\GrupoFamiliar::findOne($familia);  
            if(!$modelFamilia)
                throw new GralException('Grupo Familiar inexistente.');
        
            $servicioEnConvenio = Yii::$app->request->get('servicios');
            if(empty($servicioEnConvenio))
                $serviciosDelConvenio = [];
            else    
                $serviciosDelConvenio = explode(",", $servicioEnConvenio);
            
            $modelConvenionPago = new ConvenioPago();
            $modelConvenionPago->id_familia = $modelFamilia->id;
            
            if(isset($_POST['CuotaConvenioPago']) && count($_POST['CuotaConvenioPago']) > 0){ 
                $modelCuotasConvenioPago = array();
                foreach($_POST['CuotaConvenioPago'] as $key => $one){               
                    $modelCuotasConvenioPago["$key"] = new CuotaConvenioPago();
                }
                Model::loadMultiple($modelCuotasConvenioPago, Yii::$app->request->post());
                Model::validateMultiple($modelCuotasConvenioPago);               
            }else{
                $modelCuotasConvenioPago = array(); //array de modelos Investigados para la carga masiva
                $newModelCuotaConvenio = new CuotaConvenioPago(); 
                $newModelCuotaConvenio->id_estado = \app\models\EstadoServicio::ID_ABIERTA;
                $modelCuotasConvenioPago[0] = $newModelCuotaConvenio;   
            }
            
            $total=0;
            
            if(empty($servicioEnConvenio)){
                $modelConvenionPago->con_servicios='0';
                $dataProvider = null;
            }else{                
                $modelConvenionPago->con_servicios='1';
                
                $query = \app\models\search\ServicioAlumnoSearch::find();
                $query->alias('t');
                $query->joinWith(['servicio so']);
                $query->where(['IN', 't.id', $serviciosDelConvenio]);
                $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                ]);
                foreach($serviciosDelConvenio as $idservicio){                    
                    $modelServicioAlumno = \app\models\ServicioAlumno::findOne($idservicio);
                    $saldo = (float) $modelServicioAlumno->importeAbonar;                    
                    $total+=$saldo;
                }                    
            }
                    
            $modelConvenionPago->saldo_pagar = $total;           
                
            if($modelConvenionPago->load(Yii::$app->request->post()))
            { 
                $response = Yii::$app->serviceConvenioPago->altaConvenioPago($modelConvenionPago, $serviciosDelConvenio,$modelCuotasConvenioPago);
                if($response['success']){  
                    $transaction->commit();
                    Yii::$app->session->setFlash('ok', Yii::$app->params['cargaCorrecta']); 
                    //$this->actionEnviarCorreoCP($modelConvenionPago->id);
                    return $this->redirect(['view', 'id' => $response['nuevoModeloConvenioPago']->id]);    
                }else{
                    $transaction->rollBack();
                    $modelConvenionPago->addErrors($response['error_modelConvenioPago']);
                }
            }               
        }catch(\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error','ERROR EN LA GENERACION DEL CONVENIO DE PAGO. ' .$e->getMessage());
            $this->redirect(['administrar']);
        }catch(\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error','ERROR EN LA GENERACION DEL CONVENIO DE PAGO!!!');
            $this->redirect(['administrar']);
        } 
        
        return $this->render('altaConvenio',[ 
            'modelFamilia'=>$modelFamilia,
            'modelConvenionPago'=>$modelConvenionPago,
            'modelCuotasConvenioPago'=>$modelCuotasConvenioPago,
            'dataProvider'=>$dataProvider,
        ]);
        
        
    }
    
    
    
    /******************************************************/    
    /*
     * Edita un determinado convenio de pago; dando la posibilidad d eagregar o quitar servicios;
     * y modificar cuotas que esten en estado abiertas;
     * para esto renderizs el form conveniente para dicha tarea
     */
    public function actionEditarPlanPago(){ 
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $modelConvenio = $this->findModel(Yii::$app->request->get('id'));
            if(!$modelConvenio)
                throw new GralException('Convenio Pago inexistente.');
        
            $modelFamilia = \app\models\GrupoFamiliar::findOne($modelConvenio->id_familia);  
            if(!$modelFamilia)
                throw new GralException('Grupo Familiar inexistente.');
        
            /* analizamos las cuotas del convenio de pago */
            if(isset($_POST['CuotaConvenioPago']) && count($_POST['CuotaConvenioPago']) > 0){ 
                $modelCuotasConvenioPago = array();
                foreach($_POST['CuotaConvenioPago'] as $key => $one){
                  
                    if(isset($one['id']) && !empty($one['id'])){
                        $modelCuotasConvenioPago["$key"] =  CuotaConvenioPago::findOne($one['id']);
                    }else{
                        $modelCuotasConvenioPago["$key"] = new CuotaConvenioPago();
                    }
                    
                    
                }
            
                Model::loadMultiple($modelCuotasConvenioPago, Yii::$app->request->post());
                Model::validateMultiple($modelCuotasConvenioPago);               
            }else{
                $modelCuotasConvenioPago = CuotaConvenioPago::find()->andWhere(['id_conveniopago'=>$modelConvenio->id])->all(); 
                //$modelCuotasConvenioPago[0] = new CuotaConvenioPago();   
            }
            
            /****** logica para la adhesion de los servicios      ************/
            $serviciosSelectConvenio = [];
            
            if(isset($_GET['servicios'])){
                $servicioEnConvenio = Yii::$app->request->get('servicios');
                if(!empty($servicioEnConvenio)){
                    if(!empty($servicioEnConvenio)){               
                        $servicios = explode(',', $servicioEnConvenio);
                    }
                }
                else
                    $servicios = [];
            }else{
               $serviciosEnConvenio = ServicioConvenioPago::find()->andWhere(['id_conveniopago'=>$modelConvenio->id])->all(); 
               $servicios = \yii\helpers\ArrayHelper::getColumn($serviciosEnConvenio, 'id_servicio');
               $servicioEnConvenio = implode (",",$servicios);
            }   
                       
           
            /*
             * Buscamos los servicios impagos del servicio
             */
            $modelSearchServiciosImpagos = new \app\models\ServicioAlumno();
            $modelSearchServiciosImpagos->load(Yii::$app->request->get());
            $serviciosImpagos  = Yii::$app->serviceServicioAlumno->getDevolverServiciosImpagosLibres($modelSearchServiciosImpagos, $modelFamilia->id, null, $servicios); 
          
            
            /*
             * Buscamos los servicios a adherir al convenio 
             */
            $queryServiciosAdheridos = \app\models\ServicioAlumno::find();
            $queryServiciosAdheridos->joinWith('miAlumno a');
            
            if(empty($servicios)){
                $queryServiciosAdheridos->andFilterWhere(['in', \app\models\ServicioAlumno::tableName(). '.id' ,  ['']]);
            }
            else{
                $queryServiciosAdheridos->andFilterWhere(['in', \app\models\ServicioAlumno::tableName(). '.id' ,  $servicios]); 
                $queryServiciosAdheridos->andFilterWhere(['a.id_grupofamiliar' => $modelFamilia->id]);       
            }    
            
            $serviciosAdheridos = new \yii\data\ActiveDataProvider([
                'query' => $queryServiciosAdheridos,
                'sort' => ['defaultOrder'=>'id desc'],
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
            
             $filterDropFamilia = \app\models\Alumno::getAlumnosFamiliaDrop($modelFamilia->id);
            /***************************************************************/
            
            
            
            if($modelConvenio->load(Yii::$app->request->post()))
            {   
                $servicioEnConvenio = Yii::$app->request->get('servicios');
                if(empty($servicioEnConvenio))
                    $serviciosDelConvenio = [];
                else    
                    $serviciosDelConvenio = explode(",", $servicioEnConvenio);
                
                $response = Yii::$app->serviceConvenioPago->editarConvenioPago($modelConvenio->id,$modelConvenio, $serviciosDelConvenio, $modelCuotasConvenioPago);
                if($response['success']){  
                    $transaction->commit();
                    Yii::$app->session->setFlash('ok', Yii::$app->params['cargaCorrecta']); 
                    //$this->actionEnviarCorreoCP($modelConvenionPago->id);
                    return $this->redirect(['view', 'id' => $response['nuevoModeloConvenioPago']->id]);    
                }else{
                    $transaction->rollBack();
                    $modelConvenio->addErrors($response['error_modelConvenioPago']);
                }
            }               
 

                
                
            
        }catch(\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error','ERROR EN LA GENERACION DEL CONVENIO DE PAGO. ' .$e->getMessage());
            $this->redirect(['administrar']);
        }catch(\Exception $e){
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error','ERROR EN LA GENERACION DEL CONVENIO DE PAGO!!!');
            $this->redirect(['administrar']);
        } 
        
        return $this->render('altaEditConvenio',[ 
            'modelFamilia'=>$modelFamilia,
            'modelConvenionPago'=>$modelConvenio,
            'modelCuotasConvenioPago'=>$modelCuotasConvenioPago,
            'serviciosImpagos'=>$serviciosImpagos,
            'serviciosAdheridos' => $serviciosAdheridos,
            'servicioEnConvenio'=>$servicioEnConvenio,
            'modelSearchServiciosImpagos'=>$modelSearchServiciosImpagos,
            'filterDropFamilia' =>$filterDropFamilia
        ]);
        
        
    }
    
    /**************************************************************/
    /****************************************************************/
    /*
     * Agrega un registro modelo de datos Investigado al formulario de una solicitud.
     * Es invocado a traves de una peticion ajax, y renderiza campos input,
     * en el formulario del que es llamado.
     */
    public function actionAddCuota(){
        try{
            $modelCuota = new CuotaConvenioPago();
            $modelCuota->id_estado = \app\models\EstadoServicio::ID_ABIERTA;
            
            $nro_inv =  (int) $_POST['nro'];               

            if (Yii::$app->request->isAjax){  
                Yii::$app->response->format = 'json';
                return ['status' => 'formulario', 'error' => '0',
                        'vista' => $this->renderAjax('_formCuota', ['model' => $modelCuota,'ordn'=>$nro_inv])]; 
            }
        }catch(\Exception $e){ 
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw new \yii\web\HttpException(500, 'Error al querer adherir otra cuota');
        }
    }
    
 
    /*************************************************************/
    /*************************************************************/
    private function armarPdfconvenio($idConvenio) {
        ini_set('memory_limit', -1);
        try{
            $convenio = $this->findModel($idConvenio);
            if(empty($convenio))
                throw new GralException('No existe el convenio de pago armar laplantilla del pdf');
            
            $clienteConvenio = \app\models\GrupoFamiliar::findOne($convenio->id_familia);

            $queryServicios = ServicioConvenioPago::find();
            $queryServicios->andFilterWhere(['id_conveniopago' => $convenio->id]);
            $misServicios = $queryServicios->all(); 
            //$misServicios = new ActiveDataProvider(['query' => $queryServicios]);
            //$misServicios = $misServicios->getModels();

            $queryCuotas = CuotaConvenioPago::find();
            $queryCuotas->andFilterWhere(['id_conveniopago' => $convenio->id]);
            $misCuotas= $queryCuotas->all();
            //$misCuotas = new ActiveDataProvider(['query' => $queryCuotas]);
            //$misCuotas = $misCuotas->getModels();

            $html = '<table repeat_header="1"  cellpadding="1" cellspacing="1" width="100%" border="1">';
            $html .= '<thead>                  
              <tr>
                    <th align="center"><b> CONVENIO PAGO Nº '. $idConvenio.' <br />' . date('d-m-Y') . '</b></th>
                    <th align="center"><img src="./images/logodonbsco2.png" width="75" height="75" /></th>
              </tr>
              <tr>
                    <th style="padding:5px;" colspan="2"> <b>FAMILIA: </b>' . $clienteConvenio->apellidos . ', ' . $clienteConvenio->folio . '<br /><br />
                     <span class="datAbog"> <b> Responsable:  </b>' . $clienteConvenio->miResponsableCabecera . '</span><br />
                   </th>
              </tr>
            </thead>';

            $html .= '<tbody>';
            
            $contador = count($misServicios);
            if ($contador > 0) {          
                $html .= '<tr><td colspan="2" style="padding-left: 10px;">';
                $html .= '<b>SERVICIOS ADHERIDOS:</b> <br /><br />';
                foreach($misServicios as $servicioConvenio){
                    $idServicioAlumno = $servicioConvenio->id_servicio;
                    $servicioTomado = \app\models\ServicioAlumno::findOne($idServicioAlumno);
                    $filaDetalleServicio = $servicioTomado->datosMiServicio;
                    $filaDetalleServicio.=' / ' . $servicioTomado->miAlumno->miPersona->apellido ." ".$servicioTomado->miAlumno->miPersona->nombre;
                    $filaDetalleServicio.=' / Importe: $ ' . number_format($servicioTomado->importeRestante,2);
                    $html .= "<span style='display:block; padding-top:20mm; padding-left:15px; padding-right:15mm;'>" . $filaDetalleServicio .'</span><br />';
                }
                $html .= '</td></tr>';
            } else {
                $html .= '<tr><td colspan="2" style="padding-left: 15px;">';
                $html .= '<b>Detalle Pago:</b><br /><br />';
                $html .= $convenio->descripcion;
                $html .= '</td></tr>';
            }

            $i = 0;
            $contadorCuotas = count($misCuotas);

            if ($contadorCuotas > 0) {
                $html .= '<tr><td colspan="2">';
                $html .= '<table cellpadding="1" cellspacing="1" width="100%">
                    <thead> 
                      <tr align="center" style="background:rgb(210,115,115); font-weight:bold;  text-align:center; font-size: 16px;">
                        <td  colspan="5"> Detalle Cuotas </td>
                      </tr>
                      <tr align="center" style="background:rgb(210,115,115); font-weight:bold;  text-align:center; font-size: 16px;">
                        <td  style="background:rgb(210,115,115); text-align:center; font-weight:bold; font-size: 14px;" width="10%"> Nro </td>
                        <td  style="background:rgb(210,115,115); text-align:center; font-weight:bold; font-size: 14px;" width="12%"> Vencimiento Pago</td>
                        <td  style="background:rgb(210,115,115); text-align:center; font-weight:bold; font-size: 14px;" width="48%"> Detalle Abono </td>                   
                        <td  style="background:rgb(210,115,115); text-align:center; font-weight:bold; font-size: 14px;" width="15%"> IMPORTE </td>
                        <td  style="background:rgb(210,115,115); text-align:center; font-weight:bold; font-size: 14px;" width="15%"> IMP.ABONADO </td>
                      </tr>
                    </thead>';
                $html .= '<tfoot><tr><td colspan="4" align="left" style="padding-top:8px;padding-bottom:8px;">';
                $html .= 'MONTO TOTAL: ' . $convenio->saldo_pagar;
                $html .= '</td></tr></tfoot>';

                $html .= '<tbody>';
                
                foreach($misCuotas as $modelCuotaConvenio){    
                    $html .= '';
                    $html .= '
                                <tr class="odd">
                                  <td class="odd" style="text-align:center;  font-size: 10px;"  width="9%">&nbsp;' . $modelCuotaConvenio->nro_cuota . '</td>
                                  <td class="odd" style="text-align:center;    font-size: 10px;"  width="16%">&nbsp;' . \Yii::$app->formatter->asDate($modelCuotaConvenio->fecha_establecida) . '</td>';
                    if ($modelCuotaConvenio->id_estado == \app\models\EstadoServicio::ID_ABIERTA)
                        $html .= '<td class="odd" style="text-align:center;    font-size: 10px;"  width="16%">PENDIENTE</td>';
                    else
                        $html .= '<td class="odd" style="text-align:center;    font-size: 10px;"  width="16%">ABONADA</td>';

                    $html.= '<td class="odd" style="text-align:center;  font-size: 10px;"  width="17%">&nbsp;' . $modelCuotaConvenio->monto . '</td>';
                    $html.= '<td class="odd" style="text-align:center;  font-size: 10px;"  width="17%">&nbsp;' . $modelCuotaConvenio->importe_abonado . '</td>';
                    $html.= '</tr>';
                    $i++;
                }

                $html .= '</tbody></table></td></tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';

            return $html;
        }catch(GralException $e)
        {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error','ERROR EN LA GENERACION PDF CONVENIO DE PAGO!!!');
            $this->redirect(['administrar']);
        }catch(\Exception $e)
        {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            Yii::$app->session->setFlash('error','ERROR EN LA GENERACION DEL PDF CONVENIO DE PAGO!!!');
            $this->redirect(['view', 'id'=>$idConvenio]);
        }
    }
    
    public function actionPdf($id){
        try{   
            $plantilla = $this->armarPdfconvenio($id);
            
            $carp_cont = Yii::getAlias('@webroot') . "/archivos_generados"; //carpeta a almacenar los archivos
            $nombre_archivo = "convenioPago".Yii::$app->user->id .".pdf";
            $archivo = $carp_cont.'/'.$nombre_archivo;
            
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE, 
                'format' => Pdf::FORMAT_A4, 
                'orientation' => Pdf::ORIENT_PORTRAIT, 
                'destination' => Pdf::DEST_BROWSER,                 
                'cssInline' => '.kv-heading-1{font-size:18px}', 
                'options' => ['title' => 'Convenio de Pago'],
                'methods' => [ 
                    'SetHeader'=>['Convenio de Pago'], 
                    'SetFooter'=>['{PAGENO}'],
                ]
            ]);                
              
            $pdf->output($plantilla,$archivo,'F');
            $url_pdf = \yii\helpers\Url::to(['/convenio-pago/down-pdf','name'=>$nombre_archivo]);                
            Yii::$app->response->format = 'json';
            return ['result_error' => '0', 'result_texto' => $url_pdf];                
        }catch(\Exception $e)
        {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw new \yii\web\HttpException('500','ERROR AL GENERAR EL ARCHIVO!!!');
        }
    }//fin PdfConvenio 
    
    public function actionDownPdf($name){
        $carp_cont = Yii::getAlias('@webroot') . "/archivos_generados"; //carpeta a almacenar los archivos
        $archivo = $carp_cont.'/'.$name;
       
        if (is_file($archivo))
        {
            $size = filesize($archivo);
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=convenioPago.pdf");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . $size);     
            readfile($archivo);            
        }
    } // FIN DescargaPdfConvenio    
    
    
    /*******************************************************************/
    public function actionEnviarCorreo(){
        try{        
            $id = Yii::$app->request->get('id');   
            $model = $this->findModel($id);
            
            $plantilla = $this->armarPdfconvenio($id);
            
            $carp_cont = Yii::getAlias('@webroot') . "/archivos_generados"; //carpeta a almacenar los archivos
            $nombre_archivo = "convenioPago".Yii::$app->user->id .".pdf";
            $archivo = $carp_cont.'/'.$nombre_archivo;
            
            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE, 
                'format' => Pdf::FORMAT_A4, 
                'orientation' => Pdf::ORIENT_PORTRAIT, 
                'destination' => Pdf::DEST_BROWSER,                
                'cssInline' => '.kv-heading-1{font-size:18px}', 
                'options' => ['title' => 'Detalle Convenio Pago'],
                'methods' => [ 
                    'SetHeader'=>['Detalle Convenio Pago'], 
                    'SetFooter'=>['{PAGENO}'],
                ]
            ]);     
            $pdf->output($plantilla,$archivo,'F');
            
            $grupoFamiliar = $model->familia;
            $responsablesGrupoFamiliar = $grupoFamiliar->responsables;
            
            $resultError = 0;
            $resultMensaje = '';
            
            if(!empty($responsablesGrupoFamiliar)){
                
                $mensaje = "Estimado en el día de la fecha dío de alta el Convenio de Pago Nro: ".$model->id;      
                $mensaje .= "<br /> Se adjunta el detalle de su liquidez"; 
                
                foreach ($responsablesGrupoFamiliar as $responsable){
                    $correoCliente = $responsable->persona->mail;
                    if(!empty($correoCliente)){
                        if (Yii::$app->mailer->compose('layouts/html', ['content' => $mensaje])
                                ->setFrom([ Yii::$app->params['noreplyEmail'] => Yii::$app->params['noreplyTitle']])
                                ->setTo($correoCliente)                           
                                ->setSubject('Detalle Convenio Pago')
                                ->attach($archivo)
                                ->send()){
                            $resultMensaje.= 'Se envió un mensaje de correo a: ' . $responsable->persona->apellido .", ".$responsable->persona->nombre;
                            $resultMensaje.='<br />';
                        }else{
                            $resultMensaje.= 'No se pudo enviar un mensaje de correo a: ' . $responsable->persona->apellido .", ".$responsable->persona->nombre;
                            $resultMensaje.='<br />';                            
                        }
                    }
                }
            }
        }catch(\Exception $e)
        {
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e));
            throw new GralException('Error a enviar el correo.');           
        }
        
        Yii::$app->response->format = 'json';
        return ['error' => $resultError, 'mensaje' => $resultMensaje];
        
        
    }    
    
    /*******************************************************************/
    /******************** exportacion a excel **************************/            
    public function cellColor($objPHPExcel,$cells,$color)
    {
        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array('type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'startcolor' => array('rgb' => $color) ));
    }  
    
    public function exportarListado() 
    {  
        try{
            
            $searchModel = new ConvenioPagoSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams); 
            $dataProvider->setPagination(false);        
      
            $data = $dataProvider->getModels();           
            
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
            /*$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);*/

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
            /*$this->cellColor($objPHPExcel, 'K1', 'F28A8C');
            $this->cellColor($objPHPExcel, 'L1', 'F28A8C');
            $this->cellColor($objPHPExcel, 'M1', 'F28A8C');*/

            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Nro');
            $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Nombre/Descripcion');
            $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Fecha Alta');
            $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Folio');
            $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Familia');
            $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Saldo');
            $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Deb. Automatico');
            $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Cant. Cuotas');
            $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Cuotas Impagas');
            $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Saldo Abonado');

            $letracolumnainicio = 'A';
            $letrafilainicio = 3;
                if(!empty($data)){
                 
                foreach($data as $convenio) {
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
                    /*$columnaK = 'K' . $letrafilainicio1;
                    $columnaL = 'L' . $letrafilainicio1;
                    $columnaM = 'M' . $letrafilainicio1;*/

                    $objPHPExcel->getActiveSheet()->setCellValue($columnaA, $convenio->id);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaB, $convenio->nombre);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaC, \app\helpers\Fecha::formatear($convenio->fecha_alta, 'Y-m-d','d-m-Y')  );
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaD, $convenio->familia->folio);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaE, $convenio->familia->apellidos);
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaF, $convenio->saldo_pagar );

                    if ($convenio->deb_automatico == '1') {                        
                        $objPHPExcel->getActiveSheet()->setCellValue($columnaG, "SI");
                    } else {                       
                        $objPHPExcel->getActiveSheet()->setCellValue($columnaG, "NO");
                    }

                    $objPHPExcel->getActiveSheet()->setCellValue($columnaH, $convenio->cantCuotas );
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaI, $convenio->cuotasPendientes );
                    $objPHPExcel->getActiveSheet()->setCellValue($columnaJ, $convenio->getSaldoAbonado() );
                    $i = $i + 1;
                    $letrafilainicio += 1;
                }  
            }
   
            $carp_cont = Yii::getAlias('@webroot') . "/archivos_generados"; //carpeta a almacenar los archivos
            $nombre_archivo = "convenioPago" . Yii::$app->user->id . ".xlsx";                                
            $ruta_archivo = $carp_cont . "/" . $nombre_archivo;

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
            $writer->save($ruta_archivo);          

            $url_pdf = \yii\helpers\Url::to(['down-padron-excel', 'archivo' => $nombre_archivo]);               
            return $this->redirect($url_pdf);    
        }catch (\Exception $e) {           
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
            Yii::$app->session->setFlash('error', Yii::$app->params['errorExcepcion']);
            return $this->redirect(['site/index']);            
        }  
    }

    
    
    
    
    
    /******************************************************/
    //esto estaba cuando usabamos sessiones
    //public function actionGenerarPlanPago1(){ 
        /*
         * Establece los servicios de alumno a integrar en el convenio d epago
         */
//        $servicioEnConvenio = Yii::$app->request->get('servicios');
//        if(empty($servicioEnConvenio))
//            $serviciosDelConvenio = [];
//        else
//            $serviciosDelConvenio = explode(",", $servicioEnConvenio);
//                
//        try{
//            $transaction = Yii::$app->db->beginTransaction();
//                    
//            $familia = Yii::$app->request->get('familia');
//            $modelFamilia = \app\models\GrupoFamiliar::findOne($familia);  
//            if(!$modelFamilia)
//                throw new NotFoundHttpException('Grupo Familiar inexistente.');
//            
//            $modelConvenionPago = new ConvenioPago();
//            $modelConvenionPago->id_familia = $modelFamilia->id;
//            
//            if(isset($_POST['CuotaConvenioPago']) && count($_POST['CuotaConvenioPago']) > 0){ 
//                $modelCuotasConvenioPago = array();
//                foreach($_POST['CuotaConvenioPago'] as $key => $one){               
//                    $modelCuotasConvenioPago["$key"] = new CuotaConvenioPago();
//                }
//                Model::loadMultiple($modelCuotasConvenioPago, Yii::$app->request->post());
//                Model::validateMultiple($modelCuotasConvenioPago);               
//            }else{
//                $modelCuotasConvenioPago = array(); //array de modelos Investigados para la carga masiva
//                $modelCuotasConvenioPago[0] = new CuotaConvenioPago();   
//            }
//            
//            $total=0;
//            
//           
//            
//            if(empty($servicioEnConvenio)){
//                $modelConvenionPago->con_servicios='0';
//                $dataProvider = null;
//            }else{
//                
//                $modelConvenionPago->con_servicios='1';
//                $query = \app\models\search\ServicioAlumnoSearch::find();
//                $query->alias('t');
//                $query->joinWith(['servicio so']);
//                $query->where(['IN', 't.id', $serviciosDelConvenio]);
//                $dataProvider = new ActiveDataProvider([
//                    'query' => $query,
//                ]);
//
//                foreach($serviciosDelConvenio as $idservicio){
//                    
//                    $modelServicioAlumno = \app\models\ServicioAlumno::findOne($idservicio);
//                    $saldo = (float)$modelServicioAlumno->importeAbonar;
//                    
//                    $total+=$saldo;
//                }                    
//            }
//                    
//            $modelConvenionPago->saldo_pagar = $total;           
//                
//            if($modelConvenionPago->load(Yii::$app->request->post()) &&  $modelConvenionPago->save())
//            { 
//                $totalcuotas = 0;
//                $valid = true;
//                $nrocuota = 1;
//                    
//                    foreach($modelCuotasConvenioPago as $key => $cuota){
//                        $cuota->id_conveniopago = $modelConvenionPago->id;
//                        $cuota->nro_cuota = $nrocuota;
//                        $cuota->importe_abonado='0';
//                        $cuota->id_estado= \app\models\EstadoServicio::estadoServicio_ABIERTA;
//                        $nrocuota+=1;
//                        $totalcuotas+= ($cuota->monto)?$cuota->monto:0;
//                        $valid = $valid && $cuota->save();
//                    }
//                    
//                    if($modelConvenionPago->saldo_pagar != $totalcuotas){
//                        $valid = false;
//                        $modelConvenionPago->addError('saldo_pagar','El saldo a pagar debe coincidir con el monto total de las cuotas!!!');
//                    }
//                    
//                    if(!empty($serviciosDelConvenio)){
//                        foreach($serviciosDelConvenio as $idservicio){
//                            $modelServicioAlumno = \app\models\ServicioAlumno::findOne($idservicio);
//                            $modelServicioAlumno->id_estado = \app\models\EstadoServicio::estadoServicio_EN_CONVENIOPAGO;
//                            $modelServicioCP = new ServicioConvenioPago();
//                            $modelServicioCP->id_conveniopago = $modelConvenionPago->id;
//                            $modelServicioCP->id_servicio = $modelServicioAlumno->id;
//                            $valid = $valid && $modelServicioCP->save() && $modelServicioAlumno->save();                   
//                        }
//                    }
//                    
//                    if($valid){
//                        $transaction->commit();
//                        Yii::$app->session->setFlash('ok', Yii::$app->params['cargaCorrecta']); 
//                        //$this->actionEnviarCorreoCP($modelConvenionPago->id);
//                        return $this->redirect(['view', 'id' => $modelConvenionPago->id]);   
//                    }                       
//                
//            }    
//            
//                            
//        }
//        catch(\Exception $e){
//            Yii::$app->session->setFlash('error','ERROR EN LA GENERACION DEL CONVENIO DE PAGO!!!');
//            $this->redirect(['alta']);
//        } 
//        
//        return $this->render('altaConvenio',[ 
//            'modelFamilia'=>$modelFamilia,
//            'modelConvenionPago'=>$modelConvenionPago,
//            'modelCuotasConvenioPago'=>$modelCuotasConvenioPago,
//            'dataProvider'=>$dataProvider,
//        ]);
//    }
}
