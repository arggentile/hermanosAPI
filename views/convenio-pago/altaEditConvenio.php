<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\grid\GridView;


use app\assets\ConvenioPagoAssets;
ConvenioPagoAssets::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\ConvenioPago */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Alta Convenio Pago';
?>
<div class="box box-default box-colegio">
    <div class="box-header with-border">
        <i class="fa fa-handshake-o fa-2"></i><h3 class="box-title"> Alta Convenio </h3>
    </div>
    <div class="box-body">    

    <?php   \yii\widgets\Pjax::begin([
    'id'=>'pjax-servicios-convenio', 
    'timeout' => false, 
    'enablePushState' => false,
    ]);?>
        
    <div class="convenio-pago-form">
        
            <?php $form = ActiveForm::begin([
                'id'=>'form-convenio',
                'type' => ActiveForm::TYPE_HORIZONTAL
            ]); ?>
        
        <input type="hidden" name="ordn" id="ordn" value="<?php echo count($modelCuotasConvenioPago); ?>" />
        <input type="hidden" name="urlreload" id="urlreload" value="<?= yii\helpers\Url::to(['/convenio-pago/editar-plan-pago','id'=>$modelConvenionPago->id]); ?>" />
        <input type="hidden" name="url-generar-planpago" id="url-generar-planpago" value="<?= yii\helpers\Url::to(['/convenio-pago/editar-plan-pago']); ?>" />
        <input type="hidden" name="familiaconvenio" id="familiaconvenio" value="<?= $modelFamilia->id; ?>" />
        <input type="hidden" name="serviciosconvenio" id="serviciosconvenio" value="<?= $servicioEnConvenio; ?>" />
        
        <div class="row">
            <div  class="col-md-8 col-md-offset-2">
                <div class="row">                    
                        <?= $form->field($modelConvenionPago, 'nombre')->textInput() ?>                   
                </div>
                <div class="row">                   
                        <?= $form->field($modelConvenionPago, 'xfecha_alta')->widget(
                            DatePicker::className(),([
                                                'language'=>'es',
                                                'removeButton' => false,                                
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'dd-mm-yyyy',
                                                    'endDate' => date('d/m/Y'),
                                                ]
                                            ]));?>

                    
                </div>
                <div class="row">                    
                        <?= $form->field($modelConvenionPago, 'saldo_pagar')->textInput() ?>                   
                </div>
                <div class="row">                   
                        <?= $form->field($modelConvenionPago, 'deb_automatico')->dropDownList(['0'=>'NO','1'=>'SI'],
                             ['prompt'=>'Seleccione...'])->label('Débito.Autom'); ?>                    
                </div>
                <?php if($modelConvenionPago->con_servicios=='0'){?>
                <div class="row">                   
                        <?= $form->field($modelConvenionPago, 'descripcion')->textarea() ?>                    
                </div>
                <?php } ?>
            </div>
        </div>
        
        <hr />
        
        <div class="row" id="groupCuotas">
            <div  class="col-md-8 col-md-offset-2">
                <?= Html::button('<span class="glyphicon glyphicon-plus"></span> Agregar Cuota',
                                ['class' => 'btn btn-info btn-xs','id'=>'btn-add-cuota',
                                 'onclick'=>'js:{addCuota(\''. yii\helpers\Url::to(['convenio-pago/add-cuota']).'\');}']); ?>  
            </div>            
            <div class="col-md-8 col-md-offset-2" id="misCuotas">
                <?php
                    if(!empty($modelCuotasConvenioPago)){
                        foreach ($modelCuotasConvenioPago as $key => $cuota){                          
                            echo $this->render('_formCuota',['model'=>$cuota, 'ordn'=>$key]);
                        }
                    }
                ?>
            </div>     
        </div> 
        
        <br />
        
        <div class="box box-greenlightsite box-colegio">
            <div class="box-header with-border">
                <h3 class="box-title text-bold"> Servicios Adheridos Convenio </h3> 
               
            </div>
            <div class="box-body">
                <input type="hidden" name="urlreload" id="urlreload" value="<?= yii\helpers\Url::to(['/convenio-pago/alta-servicios']); ?>" />
                <input type="hidden" name="url-generar-planpago" id="url-generar-planpago" value="<?= yii\helpers\Url::to(['/convenio-pago/generar-plan-pago']); ?>" />
                <input type="hidden" name="familiaconvenio" id="familiaconvenio" value="<?= $modelFamilia->id; ?>" />
                <input type="hidden" name="serviciosconvenio" id="serviciosconvenio" value="<?= $servicioEnConvenio; ?>" />
            
                <?=           
                GridView::widget([
                    'id'=>'gridServiviosAdheridosCP',
                    'dataProvider' => $serviciosAdheridos,                
                    'columns' => [     
                        [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{quitar-servicio}',
                                    'buttons' => [
                                            'quitar-servicio' => function ($url, $model, $key) {                                               
                                                    return Html::a('Quitar', null, [ 'class' => 'btn btn-quitarservicio btn-xs ', 'onclick' => 'quitarServicio('.$model->id.');' ]);
                                            }
                                    ],
                                    'contentOptions' => ['nowrap'=>'nowrap']
                            ],
                        [
                            'label' => 'Servicio',
                            'attribute'=>'id_servicio',
                            'value' => function($model) {
                                return $model->datosMiServicio;
                            },
                        ],  
                        [
                            'label' => 'Alumno',
                            'attribute'=>'id_alumno',
                            'value' => function($model) {
                                return $model->datosMiAlumno;
                            },
                        ],          
                        'importe_servicio',
                        'importe_descuento',
                        'importe_abonado',
                        'importeAbonar',


                    ],
                ]); 
                ?>
                
            </div>
        </div>
           
        <div class="box box-greenlightsite box-colegio">
            <div class="box-header with-border">
                <h3 class="box-title text-bold"> Servicios Adeudados </h3> 
            </div>
            <div class="box-body">            
                <?=
                GridView::widget([
                    'id'=>'gridServiviosCP',
                    'dataProvider' => $serviciosImpagos,    
                    'filterModel' => $modelSearchServiciosImpagos,
                    'columns' => [
                            [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{select}',
                                    'buttons' => [
                                            'select' => function ($url, $model, $key) {                                               
                                                    return Html::a('Adherir', null, [ 'class' => 'btn btn-asignarservicio btn-xs', 'onclick' => 'adherirServicio('.$model->id.');' ]);
                                            }
                                    ],
                                    'contentOptions' => ['nowrap'=>'nowrap']
                            ],
                        [
                            'label' => 'Servicio',
                            'attribute'=>'id_servicio',
                            'filter' => Html::activeDropDownList($modelSearchServiciosImpagos, 'id_servicio', \app\models\ServicioOfrecido::getServiciosDrop(),['class'=>'form-control','prompt' => '']),
                            'value' => function($model) {
                                return $model->datosMiServicio;
                            },
                        ],  
                        [
                            'label' => 'Alumno',
                            'attribute'=>'id_alumno',
                            'filter' => Html::activeDropDownList($modelSearchServiciosImpagos, 'id_alumno', $filterDropFamilia,['class'=>'form-control','prompt' => '']),
                            'value' => function($model) {
                                return $model->datosMiAlumno;
                            },
                        ],          
                        'importe_servicio',
                        'importe_descuento',
                        'importe_abonado',
                        'importeAbonar',
                    ],
                ]);                         
                ?>
            </div>
        </div>            
   
          
        <br />
        <br />
        <div class="row">
            <div  class="col-md-8 col-md-offset-2">
                <?= Html::submitButton('<i class=\'fa fa-save\'></i> Generar', ['id'=>'btn-envio','class' =>  'btn btn-select btnwidth100 btn-submit-envio']) ?>
            </div>
        </div>
        
        
        <?php ActiveForm::end(); ?>

    </div>
    <?php  \yii\widgets\Pjax::end(); ?>
    <br />
   
    </div>
</div>