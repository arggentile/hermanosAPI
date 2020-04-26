<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

use app\assets\EstablecimientoAssets;
EstablecimientoAssets::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Establecimiento */

$this->title = "Establecimiento " . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Establecimientos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-default box-colegio" id="establecimiento-view">
    <div class="box-header with-border">
           <i class="fa fa-university"></i> <h3 class="box-title"> <?= $model-> nombre; ?></h3> 
    </div>
    <div class="box-body">   
        
        <?php echo $this->render('_dataEstablecimiento',['model'=>$model]); ?>    
        
        
        <div class="dropdown" id="drop-menu-establecimientos">
            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuEstablecimientoView" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Opciones
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuEstablecimientoView">
                <li>
                <?php 
                    if(Yii::$app->user->can('cargarEstablecimiento'))
                        echo Html::a('<i class="fa fa-pencil"></i>   Actualizar', ['update', 'id' => $model->id], ['class' => '']);
                ?> 
                </li>
                <li>
                <?php 
                    if(Yii::$app->user->can('eliminarEstablecimiento'))    
                        echo Html::a('<i class="fa fa-trash-o"></i>   Eliminar', ['delete', 'id' => $model->id], [
                                'class' => '',
                                'data' => [
                                    'confirm' => 'Está seguro que desea eliminar el Establecimiento?',
                                    'method' => 'post',
                                ],
                            ]);    
                ?>
                </li>              
                <li role="separator" class="divider"></li>
                <li>
                <?php
                    if(Yii::$app->user->can('gestionarDivisionesEscolares'))
                        echo Html::a('<i class="fa fa-pencil"></i>   Divisiones', 'javascript:void(0)', ['id' => $model->id, 'class' => '',
                           'onclick'=>'js:establecimiento.misDivisionesEscolares("'.Url::to(['/establecimiento/mis-divisiones-escolares','establecimiento'=>$model->id]) .'");']) ?>
                </li>
                <li>
                <?php            
                    echo Html::a('<i class="fa fa-users"></i>   Alumnos', 'javascript:void(0)', ['id' => $model->id, 'class' => '',
                       'onclick'=>'js:establecimiento.misAlumnos("'.Url::to(['/establecimiento/mis-alumnos','establecimiento'=>$model->id]) .'");']) ?>
                </li>
                <li>
                <?php
                if(Yii::$app->user->can('gestionarServiciosEstablecimiento'))
                    echo Html::a('<i class="fa fa-pencil"></i>   Servicios', 'javascript:void(0)',['id' => $model->id, 'class' => '',
                       'onclick'=>'js:establecimiento.misServicios("'.Url::to(['/establecimiento/mis-servicios-ofrecidos','establecimiento'=>$model->id]) .'");']);
                ?>
                </li>
        
            </ul>
        </div>
        
        <div id="dataestablecimiento">
        </div>
        
    </div>
</div>
<?php
$this->registerJs("      
    $('body').on('establecimiento-servicioscargados',function(event, object){        
        $.pjax.reload({container: '#pjax-servicios',timeout:false, replace: false});     
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
                intro: 'Detalles Establecimiento.'
            },  
            {
                element: document.querySelector('#informacionestablecimiento'),
                intro: 'Detalle datos establecimiento.'
            },
            {
                element: document.querySelector('#drop-menu-establecimientos'),
                intro: 'Operaciones sobre el establecimiento. Seleccione una tarea a llevar a cabo.'
            },
        ]
      });
      intro.start();
} 
", \yii\web\View::POS_END,'ayuda');
?>