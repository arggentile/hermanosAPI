<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\grid\GridView;

use app\assets\ConvenioPagoAssets;
ConvenioPagoAssets::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\ConvenioPago */

$this->title = "Convenio Pago: " . $model->id;
?>

<div class="box box-default box-index">
    <div class="box-header with-border">
        <i class="fa fa-arrow-circle-o-right"></i><h3 class="box-title"> Detalles Convenio Pago </h3>
    </div>
    <div class="box-body"> 

        <div class="row"> <!-- row dettales delconvenio -->
            <div class="col-sm-8 col-sm-offset-2 col-xs-12">
                <table>
                    <tr>
                        <td width="25%"> 
                            <img class="img-responsive" src="<?php echo Yii::getAlias('@web') . "/images/convenio.png"; ?>" alt="cp_dollar" />  </td>
                        <td width="60%">
                            <h3 class="text-bold">    Convenio Nº: <?php echo $model->id; ?> </h3>
                            <span class="text-bold">  Fecha Alta: </span> <?php echo \Yii::$app->formatter->asDate($model->fecha_alta); ?><br /> 
                            <span class="text-bold">  Familia: </span> <?php echo $model->familia->apellidos ." Folio: ".$model->familia->folio; ?> <br />
                            <span class="text-bold">  Importe:  </span> $<?php echo $model->saldo_pagar; ?> <br />
                            <br />
                            <div class="">
                            <?php
                            if($model->sePuedeEditar())
                            echo Html::a('<i class="glyphicon glyphicon-pencil"> </i>', yii\helpers\Url::to(['/convenio-pago/editar-plan-pago','id'=>$model->id]), ['class' => 'btn btn-primary']);
                            echo " ";
                            if($model->sePuedeEliminar())
                            
                            echo Html::a('<i class="glyphicon glyphicon-trash"> </i>', yii\helpers\Url::to(['/convenio-pago/delete','id'=>$model->id]), ['class' => 'btn btn-danger']);
                            echo " ";
                            echo Html::button('<i class="glyphicon glyphicon-print"> </i>', ['class' => 'btn btn-info', 'id'=>'btn-pdf-convenio',
                                'onclick'=>'js:{downPdfConvenio("'. yii\helpers\Url::to(['/convenio-pago/pdf','id'=>$model->id]) .'");}']);
                            echo " ";
                            echo Html::button('<i class="glyphicon glyphicon-envelope"> </i> Enviar Correo', ['class' => 'btn btn-info', 'id'=>'btn-correo-convenio',
                                'onclick'=>'js:{enviarEmailPdfConvenio("'. yii\helpers\Url::to(['/convenio-pago/enviar-correo','id'=>$model->id]) .'");}']);
                            ?>
                            </div>
                        </td>
                    </tr>                  
                </table>          
            </div>
        </div> <!-- fin row dettales delconvenio -->
        
        <br />
        
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2 col-xs-12">
            <div class="box box-warning">             
                <div class="box-header with-border">
                    <h3 class="box-title">Cuotas</h3>
                    
                </div>     

                <div class="box-body">
                    <?php Pjax::begin(); ?>    
                    <?= GridView::widget([
                            'dataProvider' => $misCuotas,                            
                            'columns' => [                                
                                                        
                                [
                                    'label' => 'Fecha Pago',
                                    'attribute'=>'fecha_establecida',
                                    'format' =>'date',
                                    'contentOptions'=>['class'=>'columncenter'],
                                    'value' => function($model) {                                        
                                            return $model->fecha_establecida;                                        
                                    },
                                ],
                                [
                                    'label' => 'Estado',
                                    'contentOptions'=>['class'=>'columncenter'],
                                    'attribute'=>'estado',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return $model->getDescripcionEstadoCuota();
                                    },
                                ],     
                                [
                                    'label' => 'Importe',
                                    'contentOptions'=>['class'=>'columncenter'],
                                    'attribute'=>'monto',
                                    'value' => function($model) {
                                        return "$ " . $model->monto;
                                    },
                                ],
                                [
                                    'label' => 'Imp.Abonado',
                                    'contentOptions'=>['class'=>'columncenter'],
                                    'attribute'=>'monto',
                                    'value' => function($model) {
                                        return "$ " . $model->importe_abonado;
                                    },
                                ],            
                                [
                                    'label' => 'Factura',
                                    'contentOptions'=>['class'=>'columncenter'],
                                    'format'=>'raw',
                                    'value' => function($model) {
                                        if(!empty($model->id_tiket)){
                                            return 
                                                \yii\bootstrap\Html::button("<img class='img-responsive' src='".Yii::getAlias("@web")."/images/pdf.png' alt='cp_dollar' />", ['class' => 'btn btn-xs', 'id'=>'btn-pdf-servicios',
                                                'onclick'=>'js:{downFactura("'. yii\helpers\Url::to(['caja/pdf-factura','idFact'=>$model->miIdFactura]) .'");}']);
                                        }else
                                        { return "";}
                                    },
                                ],                         
                            ],
                        ]); ?>
                    <?php Pjax::end(); ?>                    
                </div>
            </div>
            </div>
        </div>
        
        <div class="row">          
            <div class="col-sm-8 col-sm-offset-2 col-xs-12">
            <div class="box box-warning">                 
                <div class="box-header with-border">
                    <h3 class="box-title">Detalle Servicios</h3>                    
                </div>
                <div class="box-body">
                
                    <?php
                    if(!empty($misServicios->getModels())){
                        Pjax::begin();    
                        echo
                        GridView::widget([
                            'dataProvider' => $misServicios,                        
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],                            
                                [
                                        'label' => 'Detalle',
                                        'attribute'=>'id_servicio',
                                        'value' => function($model) {
                                            return $model->servicio->datosMiServicio;
                                        },
                                    ],  
                                [
                                        'label' => 'Alumno',                                       
                                        'value' => function($model) {
                                            return $model->servicio->datosMiAlumno;
                                        },
                                    ],                  

                            ],
                        ]);
                        Pjax::end(); 
                    }else{
                      echo $model->descripcion;   
                    } ?>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>