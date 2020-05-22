<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\form\ActiveForm;
use yii\helpers\Url;

use app\assets\DebitoAutomaticoAssets;
DebitoAutomaticoAssets::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\DebitoAutomatico */

$this->title = 'Gestion Debito Automatico';
$this->params['breadcrumbs'][] = ['label' => 'Debito Automatico', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?php
yii\bootstrap\Modal::begin([        
    'id'=>'modalProcesa',
    'class' =>'modal-scrollbar',
    'size'=>'modal-lg',
    ]);
    echo "<div id='modalContent'>";
    echo "<div id='form-upload'>";
        
            $form = ActiveForm::begin(['id'=>'form-procesamiento',
                'action'=> Url::to(['/debito-automatico/procesar','id'=>$model->id]),
                'options'=>['enctype'=>'multipart/form-data']]); 
            
            echo "<input type='file' id='DebitoAutomatico_archivoentrante' name='DebitoAutomatico[archivoentrante]' size='40' />";
            echo Html::submitButton('<i class=\'fa fa-save\'></i> Guardar', 
                   ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary btn-procesar','data-loading-text'=>'Aguarde Procesando...']);
            
            ActiveForm::end(); 
        echo "</div>";
    echo "</div>";
yii\bootstrap\Modal::end();
?>



<div class="box box-colegio">
    <div class="box-header with-border">        
       <i class="fa fa-bell"></i>    <h3 class="box-title"> Detalle Débito Automático </h3> 
    </div>
    <div class="box-body">

      <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <table id='infodebito'>
                <tr>
                    <td width="25%"> 
                        <img class="img-responsive" src="<?php echo Yii::getAlias('@web') . "/images/convenio.png"; ?>" alt="cp_dollar" />  
                    </td>
                    <td>
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'nombre',                              
                                [
                                    'label' => 'Tipo Archivo',  
                                    'format'=>'html',
                                    'value' => function($model){
                                    if($model->tipo_archivo== app\models\DebitoAutomatico::ID_TIPODEBITO_CBU)
                                        return "Debito x CBU";
                                    else
                                        return "Debito x TC";
                                    }
                                ],    
                                [
                                    'label' => 'Periodo',  
                                    'format'=>'html',
                                    'value' => "<span class='label bg-red'>" . $model->periodoBarrido ."</span>",
                                ],                                
                                'fecha_creacion:date',
                                'fecha_debito:date',
                                [
                                    'label' => 'Reg. Enviados/Familia',
                                    'value' => $model->registros_enviados,
                                    'contentOptions' => [
                                        'class' => 'view-registrosenviados',
                                    ]
                                    
                                ],
                                [
                                    'label' => 'Reg. Correctos',
                                    'value' => $model->registros_correctos,
                                    'contentOptions' => [
                                        'class' => 'view-registroscorrectos',
                                    ]
                                ],                                   
                                [
                                    'label' => 'Procesado',  
                                    'format'=>'html',
                                    'value' => function($model){
                                        if($model->procesado=='0')
                                            return "<span class='label bg-red'> No </span>";
                                        else
                                           return "<span class='bg bg-blue'> Si </span>"; 
                                    }
                                ],
                                
                            ],
                        ]); ?>

                    </td>
                </tr>
            </table>
        </div>
      </div>
        

      <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <p>
                <?php
                if($model->getSePuedeEliminar())
                    echo Html::button('<i class="glyphicon glyphicon-trash"></i>',  
                        [ 'class' => 'btn btn-danger btn-eliminar-debito', 'data-url'=>Url::to(['/debito-automatico/eliminar','id'=>$model->id]),
                            ]); ?>
                
                <?= Html::button('<i class="glyphicon glyphicon-floppy-save"></i> Arc.Envio', ['class' => 'btn btn-primary','id'=>'btn-down-archivo',
                        'onclick'=>'js:{downArchivoBanco("'.Url::to(['/debito-automatico/descargar-archivo-envio','id'=>$model->id]) .'");}']); ?>
                
                <?= Html::button('<i class="glyphicon glyphicon-hand-right"></i>  Excel', ['value'=>yii\helpers\Url::to(['convertir-a-excel', 'id' => $model->id]), 'class' => 'btn btn-success','id'=>'btn-verificar']) ?>
            
               <?= Html::button('<i class="glyphicon glyphicon-hand-right"></i>  Procesar Devolución', ['value'=> yii\helpers\Url::to(['procesar','id'=>$model->id]), 'class' => 'btn btn-primary', 'id'=>'btn-procesa']) ?>
            </p>    
        </div>
      </div>
        
        <div class="row">
        <div class="col-sm-12 ">
    
        <?php 
            \yii\widgets\Pjax::begin([
                'id'=>'pjax-servivios-en-debitoautomatico'
            ]); ?>    
        <?=     
            \yii\grid\GridView::widget([
                    'dataProvider' => $dataMisDebitos,    
                    'filterModel' => $searchItemsDebitos,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
//                        [
//                            'label' => 'Servicio',
//                            'attribute'=>'tiposervicio',
//                            'filter' => Html::activeDropDownList($searchItemsDebitos, 'tiposervicio', [ app\models\DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO=>'Convenio Pago', app\models\DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS=>'Servicios'] ,['class'=>'form-control','prompt' => '']),
//                            'value' => function($model) {
//                                if($model->tiposervicio== app\models\DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS)
//                                    return "SERIVICIO";
//                                else
//                                    return "CUOTA CP";
//                            },
//                        ], 
                        [
                            'label' => 'Servicio',
                            'attribute'=>'tiposervicio',
                            'filter' => Html::activeDropDownList($searchItemsDebitos, 'tiposervicio', [ app\models\DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO=>'Convenio Pago', app\models\DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS=>'Servicios'] ,['class'=>'form-control','prompt' => '']),
                            'value' => function($model) {
                                
                                    return $model->detalleMiServicio;
                               
                                
                            },
                        ], 
                        [
                            'label' => 'Importe',
                            'value' => function($model) {
                                return $model->importe;
                                
                            },
                        ],             
                        [
                            'label' => 'Familia',
                            'filter' => Html::activeInput('text',$searchItemsDebitos, 'id_familia', ['class'=>'form-control']),
                            'value' => function($model) {
                                return $model->familia->folio . " " . $model->familia->apellidos;
                            },
                        ], 
                        [
                            'label' => 'Alumno/Familia',
                            'filter' => Html::activeInput('text',$searchItemsDebitos, 'id_alumno', ['class'=>'form-control']),
                            'value' => function($model) {
                                return $model->detalleAlumno;
                            },
                        ],
                        [
                            'label' => 'Resultado',
                            'value' => function($model) {
                                return $model->detalleResultadoProcesamiento;
                            },
                        ],             
                        'resultado_procesamiento',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'headerOptions' => [
                                'width' => '50',
                                'class'=>'actionsgrid'
                            ],
                            'template'=>'{viewtiket} {pdftiket}',
//                            'visibleButtons' => [                                   
//                                        'update' => Yii::$app->user->can('cargarAlumno'),
//                                        'view' =>Yii::$app->user->can('visualizarAlumno'),
//                                    ],
                            'buttons' => 
                            [
                                    'viewtiket' => function ($url, $model) {   
                                    if(!empty($model->getMiTiket()))
                                            return Html::a('<i class="fa fa-eye"> </i>', Url::to(['/caja/detalle-tiket', 'id'=>$model->getMiTiket()->id]) ,
                                                    []);
                                    }, 
                                    'pdftiket' => function ($url, $model) {
                                        if(!empty($model->getMiTiket()))
                                            return Html::a('<i class="glyphicon glyphicon-print"> </i>','javascript:void(0)' ,
                                                    ['class' => 'btn-pdf-tiket',
                                                    'onclick'=>'js:{downFactura("'.  Url::to(['/caja/pdf-tiket', 'idTiket'=>$model->getMiTiket()->id]) .'");}']);
                                    }, 
                            ],             
                        ],            
                            
                    ],
                ]); ?>
        <?php \yii\widgets\Pjax::end(); ?>
        </div>
      </div>
      
    </div>
</div>
<?php
$this->registerJs("      
function ayuda(){         
    var intro = introJs();
      intro.setOptions({
        nextLabel: 'Siguiente',
        prevLabel: 'Anterior',
        skipLabel:'Terminar',
        doneLabel:'Cerrar',
        steps: [      
            {
                element: document.querySelector('#infodebito'),
                intro: 'Detalle / Información del debito.'
            }, 
            {
                element: document.querySelector('.view-registrosenviados'),
                intro: 'Indica la cantidad de Registros/Familias enviadas a debitar.'
            },
            {
                element: document.querySelector('.view-registroscorrectos'),
                intro: 'Indica la cantidad de Registros/Familias procesados correctamente.'
            },
            {
                element: document.querySelector('#btn-down-archivo'),
                intro: 'Descargar archivo a enviar al banco.'
            },
            {
                element: document.querySelector('#btn-verificar'),
                intro: 'Descargar archivo excel.'
            },   
            {
                element: document.querySelector('#btn-procesa'),
                intro: 'Presione para procesar la devolución del banco.'
            },
            
        ]
      });
      intro.start();
}      
", \yii\web\View::POS_END,'ayuda');
?>