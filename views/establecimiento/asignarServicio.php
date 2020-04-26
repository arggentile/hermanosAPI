<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ServicioEstablecimiento */ 
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box box-default box-colegio">   
    <div class="box-body">       
        <div class="servicio-establecimiento-form">
                <div class="row">
                    <div class="col-sm-6">
                        <?= Html::activeDropDownList($model, 'id_servicio', app\models\ServicioOfrecido::getServiciosActivosConDetalle(),
                            ['prompt'=>'Seleccione...', 
                            'id'=>'miservicio', 
                            'class'=>'form-control']); ?>   
                    </div>            
                </div>  
            <div id="servicios-asign" class="">
                 <!-- solo para la ayuda -->
                <?=
                 yii\grid\GridView::widget([
                            'id' => 'grid-divisiones',
                            'dataProvider' => $dataProviderDivisiones,
                            'columns' => [

                                'nombre',
                                ['class' => 'yii\grid\ActionColumn',
                                    'template' => '{asignar} {quitar}',
                                    'headerOptions' => ['class' => 'actions-ser'],
                                    'buttons' =>
                                    [
                                    'asignar' => function ($url, $model){
                                        
                                            return Html::button( '<i class="glyphicon glyphicon-ok-circle"></i>',                            
                                                ['class' => 'btn btn-xs btn-primary btn-asignar disabled']
                                        );  
                                    },      
                                    'quitar' => function ($url, $model) {                                             
                                            return Html::button( '<i class="glyphicon glyphicon-remove-circle"></i>',                            
                                                ['class' => 'btn btn-xs btn-danger btn-quitar disabled']
                                                );
                                            },
                                    ],
                                ],
                            ],
                        ]);
                    ?>
                
            </div>
        </div>        
    </div>
</div>
<?php
$this->registerJs('
    $("#miservicio").on("change",function(){        
        var est='.$model->establecimiento.';
        var ser = $(this).val();        
        
        $.ajax({
            url: \''. yii\helpers\Url::toRoute(['/establecimiento/get-servicios']) .'\',
            data: {idServicio:+ser,idEstablecimiento:+est},
            dataType: \'json\',
            beforeSend: function(response){                
            },
            success: function(response){
                if(response.error===\'0\'){
                    console.log(response);
                    $(\'#servicios-asign\').html(response.vista);   
                }
            },
            error:function(){
                console.log("ERROR INERNO");
            }
        });
        return false;
    });
',
 yii\web\View::POS_READY,'multiselect');?>
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
                intro: 'Gestion de servicos sobre el Establecimiento. Para realizar la asociacion de los servicios a cada division escolar el mismo debe estar dado de alta previamente'
            },  
            {
                element: document.querySelector('#miservicio'),
                intro: 'Seleccione el servicio a asociar a las divisiones.'
            },
            {
                element: document.querySelector('#servicios-asign'),
                intro: 'Divisiones .'
            },
            {
                element: document.querySelector('.btn-asignar'),
                intro: 'Botón para asignar el servicio a la división.'
            },
            {
                element: document.querySelector('.btn-quitar'),
                intro: 'Botón para deshasociar el servicio a la división.'
            },

        ]
      });
      intro.start();
} 
", \yii\web\View::POS_END,'ayuda');
?>