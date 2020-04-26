<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;

use app\assets\ServicioOfrecidoAssets;
ServicioOfrecidoAssets::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\ServicioOfrecido */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Servicio Ofrecidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-default box-colegio ">
    <div class="box-header with-border">
        <i class="fa fa-cogs"></i> <h3 class="box-title"> Detalle Servicio: <b><?= $model->nombre;?> </b></h3> 
    </div>
    <div class="box-body">
        <?= $this->render('_viewDataServicioOfrecido',['model'=>$model]); ?>
        
        <p class="actionsservicio">
            
        </p>  
        <br />
        <div class="box box-primary box-colegio ">
            <div class="box-header with-border">
                <i class="fa fa-cogs"></i> <h3 class="box-title"> Devengamiento </h3>
                <p class="pull-right">
                    <?php
                    if(Yii::$app->user->can('devengarServicioOfrecido')){
                        echo Html::button('<span class="glyphicon glyphicon-piggy-bank"> </span> Devengar Servicios', ['value'=> yii\helpers\Url::to(['devengar-servicio', 'id' => $model->id]), 
                                             'class'=>'btn btn-default', 'id'=>'btn-devenarServicioOfrecido']); 
                        echo " ";
                        echo Html::button('<span class="glyphicon glyphicon-remove-circle"> </span> Quitar Devengamiento', ['value'=> yii\helpers\Url::to(['eliminar-devengamiento', 'id' => $model->id]), 
                                             'class'=>'btn btn-default', 'id'=>'btn-eliminarDevengamientoServicioOfrecido']);
                    }
                    ?>   
                </p>
            </div>
            <div class="box-body">   
                <?= 
                app\widgets\buscadorServiciosAlumno\BuscadorServiciosAlumno::widget(
                    ['searchModel' => $searchModelSerAlumnos,
                    'dataProvider'=>$dataProviderSerAlumnos,
                    'notDisplayColumn'=>['servicio']]); 
                ?>
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
                intro: 'Infomación y gestión del servicio.'
            },
            {
                element: document.querySelector('#dataservicio'),
                intro: 'Información basica.'
            },
            {
                element: document.querySelector('.actionsservicio'),
                intro: 'Botonera para su gestión.'
            },
            {
                element: document.querySelector('#btn-devenarServicio'),
                intro: 'Botón para iniciar el devengamiento.'
            },
            {
                element: document.querySelector('#btn-eliminardevengamiento'),
                intro: 'Botón para quitar el devengamiento.'
            },          
            
            {
                element: document.querySelector('#seachSA'),
                intro: 'Listan los servicios de los alumos a cuales se devengo el servicio actual.'
            }               
            
        ]
      });
      intro.start();
} 
", \yii\web\View::POS_END,'ayuda');
?>