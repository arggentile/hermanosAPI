<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;


?>
<?php
    yii\bootstrap\Modal::begin([        
        'id'=>'modal-servicios-establecimiento',
        'class' =>'modal-scrollbar',
        'size' => 'modal-lg',        
    ]);
        echo "<div id='modalContent'></div>";
    yii\bootstrap\Modal::end();
?>


<div class="row">
    <div class="col-xs-10 col-xs-offset-1"> 
<div class="box box-warning establecimiento-index">
    <div class="box-header with-border">
    </div>
    <div class="box-body">
        
            <div class="pull-right">
                <p>
                <?=  Html::button('<i class="fa fa-plus-square"></i> Cargar Servicio', 
                        //['/establecimiento/nuevo-servicio','id_establecimiento'=>$modelEstablecimiento->id], 
                        ['class' => 'btn btn-primary btn-alta','id'=>'btn-alta-servicio',
                            'onclick'=>'js:serviciosEstablecimiento.cargarServicio("'.Url::to(['/establecimiento/nuevo-servicio', 'id_establecimiento' => $modelEstablecimiento->id]) .'");']) ?>
                </p>
            </div>
            <div>
                <?php Pjax::begin([
                    'id'=>'pjax-servicios',
                    'enablePushState' => false, 
                    'timeout'=>false
                    ]); 
                ?>    
                <?= GridView::widget([
                    'id'=>'grilla-servicios',
                    'dataProvider' => $dataProviderSerEst,
                    'filterModel' => $searchModelSerEst,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'label' => 'Servicio',
                            'attribute'=>'id_servicio',
                            'filter'=> dmstr\helpers\Html::activeDropDownList($searchModelSerEst, 'id_servicio', \app\models\ServicioDivisionEscolar::getServiciosxEstablecimiento($modelEstablecimiento->id), ['prompt'=>'','class'=>'form-control']),                  
                            'value' => function($model) {
                                return $model->miServicio->detalleServicio  ;
                            },
                        ],
                        [
                            'label' => 'División Escolar',
                            'filter'=> dmstr\helpers\Html::activeDropDownList($searchModelSerEst, 'id_divisionescolar', \app\models\DivisionEscolar::getDivisionesEstablecimiento($modelEstablecimiento->id), ['prompt'=>'','class'=>'form-control']),                  
                            'attribute'=>'id_divisionescolar',
                            'value' => function($model) {
                                return $model->miDivisionescolar->nombre;
                            },
                        ],
                                    /*
                         ['class' => 'yii\grid\ActionColumn',
                                    'template' => '{quitar}',
                                    'headerOptions' => ['class' => 'actions-ser'],
                                    'buttons' =>
                                    [                                    
                                      'quitar' => function ($url, $model){
                                            
                                                return Html::button( '<i class="glyphicon glyphicon-remove-circle"></i>',                            
                                                    [    'class' => 'btn btn-xs btn-danger',
                                                        'title'=>'Remover',
                                                        'onclick' => 'js:eliminarServicioDivision("'.Url::to(['/establecimiento/quitar-servicio-division', 'division' => $model->id_divisionescolar, 'servicio' => $model->id_servicio]) .'");']
                                                    );
                                                },
                                    ],
                                ],*/
                    ],
                ]); ?>
                <?php Pjax::end(); ?>    
            </div>
    </div>
</div>
                </div>
</div>

<?php 
$this->registerJsFile('@web/js/servicios-establecimiento.js', ['depends'=>[app\assets\AppAsset::className()]]);
?>

<?php
$this->registerJs("   
    
$('#modal-servicios-establecimiento').on('hide.bs.modal', function(){    
    $('body').trigger('establecimiento-servicioscargados',{});
});


function ayuda(){         
    var intro = introJs();
      intro.setOptions({
        nextLabel: 'Siguiente',
        prevLabel: 'Anterior',
        skipLabel:'Terminar',
        doneLabel:'Cerrar',
        steps: [      
            { 
                intro: 'Listado de servicios brindados por el establecimiento.'
            },  
            {
                element: document.querySelector('.grid-view .filters'),
                intro: 'Filtros para realizar busquedas especificas, puede especificar mas de un dato'
            },  
            {
                element: document.querySelector('#btn-alta-servicio'),
                intro: 'Presione para Gestionar Servicios al establecimiento.'
            },
        ]
      });
      intro.start();
} 
", \yii\web\View::POS_END,'ayuda');
?>
