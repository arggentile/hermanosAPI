<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\widgets\ActiveField;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\web\View;
use kartik\widgets\DatePicker;
use yii\widgets\Pjax;
use yii\grid\GridView;

use app\assets\CajaAssets;
CajaAssets::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\Factura */

$this->title = 'Alta Factura';
$this->params['breadcrumbs'][] = ['label' => 'Facturas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-default box-colegio">
    <div class="box-header with-border">
        <i class="fa fa-user-plus"></i> <h3 class="box-title"> Alta Factura - Cobro Servicios </h3>
    </div>
    <div class="box-body">
        
        <?php 
        $form = ActiveForm::begin(
            [
            'id'=>'form-cobro-servicios'
            ]); ?> 
        
        
        
        
        <div class="row"> 
            <div class="col-sm-12">
                <div class="row">        
                    <div class="col-sm-6">
                        <?= $form->field($modelTiket, 'id_tipopago', ['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-usd"></i> Medio Pago']]])->dropDownList(\app\models\FormaPago::getFormasPagoHabilitadosCobrosCaja(),
                             ['prompt'=>'Seleccione...'])->label(false);
                        ?> 
                    </div>     
                </div> 
        
                <div class="row form-group datagrupofamiliar">
                    <div class="col-sm-6">        
                        <div class="input-group">
                            <span class="input-group-addon">
                            <?=
                            Html::button('Buscar Familia', 
                                    ['value'=> Url::to(['buscarFamilia']), 
                                     'class'=>'', 'id'=>'btn-buscarfamilia']);
                            ?>
                            </span>
                            <?= Html::input('text','cliente', '',['id' => 'apellidoFamilia', 'class' => 'form-control', 'readonly'=>true]); ?>
                            <?= Html::activeHiddenInput($modelTiket, 'id_cliente'); ?>
                        </div>
                    </div>                       
                </div>
                
                <div class="row form-group datagrupofamiliar">
                    <div class="col-sm-6">        
                        <div class="input-group">
                            <?= $form->field($modelTiket, 'dni_cliente'); ?>
                        </div>
                    </div>                       
                </div>
                
                <div class="row">        
                    <div class="col-sm-3">
                        <?= $form->field($modelTiket, 'xfecha_tiket')->widget(
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
                </div> 

                <div class="row">        
                    <div class="col-sm-6">
                        <?= $form->field($modelTiket, 'detalles')
                                                ->textarea(['rows' => '6']);
                        ?> 
                    </div>     
                </div> 

                <div class="row">        
                    <div class="col-sm-4">
                        <?= $form->field($modelTiket, 'importe_servicios',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-usd"></i>']]])->textInput(['readonly'=>true]);?>
                    </div>     

                    <div class="col-sm-4">
                        <?= $form->field($modelTiket, 'importe',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-usd"></i>']]])->textInput();?>
                    </div>     
                </div>
            </div>
            
            <?php
              Pjax::begin(
                ['id'=>'pjax-servicios-tiket', 
                    'timeout' => false, 
                    'enablePushState' => false,
                    'clientOptions' => ['method' => 'POST']
                ]);
            ?>
            
            <input type="hidden" name="servicios-tiket" id="servicios-tiket" value="<?= $serviciosEnTiket; ?>" />
            <input type="hidden" name="cuotascp-tiket" id="cuotascp-tiket" value="<?= $cuotasEnTiket; ?>" />
            <input type="hidden" name="urlreload" id="urlreload" value="<?= yii\helpers\Url::to(['/caja/cobrar']); ?>" />
        
            <div class="col-sm-12">
                <?=
                Html::button('Agregar Servicio', 
                        ['value'=> Url::to(['buscarDeudaFamilia']), 
                         'class'=>'btn btn-primary', 'id'=>'btn-agregar-servicio-impago']);
                ?>
             
                

            </div>
        </div> 
        <div class="form-group">
            <?= Html::submitButton("<i class='fa fa-save'></i> Guardar", ['class' => 'btn btn-primary btn-flat','id'=>'btn-envio']) ?>
        </div>
        <?php
        if(!empty($dataProvider)){
        echo GridView::widget([
                    'id'=>'grid-servicios-tiket',
                    'dataProvider' => $dataProvider,                
                    'columns' => [  
                        
                        [
                            'label' => 'TIPO SERVICIO',  
                            'attribute'=>'tiposervicio',
                            'value' => function($model) {
                                if($model['tiposervicio'] == app\models\DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO)
                                    return "Cuota CP";
                                else
                                    return "Servicios";
                            },
                        ], 
                        [
                            'label' => 'Servicio',                       
                            'value' => function($model) {
                                if($model['tiposervicio']== app\models\DebitoAutomatico::ID_TIPOSERVICIO_CONVENIO_PAGO){
                                    return \app\models\CuotaConvenioPago::getDetalleDatosCuota($model['idservicio']);
                                }    
                                if($model['tiposervicio']== app\models\DebitoAutomatico::ID_TIPOSERVICIO_SERVICIOS){
                                    return \app\models\ServicioAlumno::getDetalleDatos($model['idservicio']);
                                }

                            },
                        ],
                        [
                            'label' => 'Importe Pendiente',                                
                            'value' => function($model) {
                                return $model['importeaabonar'];
                            },
                        ],            

                    ],
                ]); 
                }
                ?>
    </div>
        
       <?php Pjax::end(); ?> 

        <?php ActiveForm::end(); ?>
        
    </div>




<?php
    yii\bootstrap\Modal::begin([        
        'id'=>'modalfamilia',
        'class' =>'modal-scrollbar',
        'size'=>'modal-lg',
        ]);
        echo "<div id='modalContent'></div>";
    yii\bootstrap\Modal::end();
?>
<?php
    yii\bootstrap\Modal::begin([        
        'id'=>'modalserviciosimpagos',
        'class' =>'modal-scrollbar',
        'size'=>'modal-lg',
        ]);
        echo "<div id='modalContent'></div>";
    yii\bootstrap\Modal::end();
?>