<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

use app\assets\ConvenioPagoAssets;
ConvenioPagoAssets::register($this);


/* @var $this yii\web\View */
/* @var $model app\models\Abogado */

$this->title = 'Alta Convenio Pago';
?>
<div class="box box-default box-colegio"> 
    <div class="box-header with-border">        
       <i class="fa fa-handshake-o fa-2"> </i> <h3 class="box-title"> Seleccion Servicios C.P   </h3> 
    </div>
        <div class="box-body">
         
            <div class="row"> <!-- row dettales delconvenio -->
                <div class="col-sm-12 col-xs-12">
                    <table>
                        <tr>
                            <td width="30%"> 
                                <img class="img-responsive" src="<?php echo Yii::getAlias('@web') . "/images/family.png"; ?>" alt="familia" />  
                            </td>
                            <td width="60%">
                              
                                    <h3 class="text-light-blue text-bold">    Familia: <?php echo $modelFamilia->apellidos; ?> </h3>
                                    <span class="text-bold">  Folio: </span> <?php echo $modelFamilia->folio; ?> <br />
                                    <span class="text-bold">  Descripción: </span> <?php echo $modelFamilia->descripcion; ?> <br />
                                    <span class="text-bold">  Pago Asociado: </span> <?php echo $modelFamilia->pagoAsociado->nombre; ?> <br />
                             </td>
                        </tr>
                    </table>  
                </div>
            </div>
                
            <br />
            <hr />
        
        
        <?php   \yii\widgets\Pjax::begin([
            'id'=>'pjax-servicios-convenio', 
            'timeout' => false, 
            'enablePushState' => false,
            ]);?>
        
        <div class="box box-greenlightsite box-colegio">
            <div class="box-header with-border">
                <h3 class="box-title text-bold"> Servicios Adheridos Convenio </h3> 
                <p class="pull-right">
                    <a href="javascript:void(0)" class="btn btn-xs btn-primary " onclick="enviarAConvenio(this);">Generar Convenio Pago</a>
                </p>
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
    <?php  \yii\widgets\Pjax::end(); ?>
          
            
            
           
        </div>
</div>
<style type="text/css">
    .btn-quitarservicio{
        background-color: rgba(225,25,10, .9);
        color: #fff;
    }
    .btn-asignarservicio{
        background-color: rgba(29, 110, 141, 0.9);
        color: #fff;
    }
</style>