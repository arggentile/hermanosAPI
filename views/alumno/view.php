<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\GridView;

use app\assets\AlumnoAssets;
AlumnoAssets::register($this); 

/* @var $this yii\web\View */
/* @var $model common\models\Alumno */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Alumnos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php    
    yii\bootstrap\Modal::begin([        
        'id'=>'modalBonificaciones',
        'header'=>'Asignación de Bonificación',
        'class' =>'modal-scrollbar', 
        'size' => 'modal-lg',
        ]);
        echo "<div id='modalContent'></div>";
    yii\bootstrap\Modal::end();
    
   
?>



<div id="alumno-view" class="box box-default box-colegio">
    <div class="box-header with-border">
        <i class="fa fa-user-plus"></i> <h3 class="box-title"> Información Alumno </h3> 
    </div>
    <div class="box-body">
        
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="row">
                    
                    <div class="col-sm-12">
                        <table>
                            <tr id='tabledataalumno'>
                                <td width="30%"> 
                                    <img class="img-responsive" src="<?php echo Yii::getAlias('@web') . "/images/avatar.png"; ?>" alt="familia" />  
                                </td>
                                <td width="60%">
                                    <h3 class="text-light-blue bold">    Legajo: <?php echo $model->nro_legajo . "  ". $model->hijo_profesor; ?> </h3>
                                    <span class="text-bold">  Apellido / Nombre: </span> <?php echo $model->persona->apellido ."; ".$model->persona->nombre; ?> <br />
                                    <span class="text-bold">  Documento: </span> <?php echo $model->persona->nro_documento; ?> <br />
                                    <span class="text-bold">  Establecimiento: </span> <?php echo $model->divisionescolar->establecimiento->nombre . "  - " . $model->divisionescolar->nombre; ?> <br />
                                    <span class="text-bold">  Activo: </span> <?php echo $model->soyActivo; ?> <br />
                                    <span class="text-bold">  Familia: </span> <?php echo Html::a("<i class='glyphicon glyphicon-eye-open'></i> ". $model->grupofamiliar->apellidos . " / " . $model->grupofamiliar->folio, ['grupo-familiar/view', 'id' => $model->id_grupofamiliar], ['class' => 'text-light-blue']); ?> <br />
                                    <?php
                                    if($model->activo)
                                        
                                        echo "<span class='text-bold '> ACTIVO </span>";
                                    else
                                        echo "<span class='text-bold text-danger'> INACTIVO </span>";
                                    ?>
                                    <br />
                                    <p id='bot-gestionalumno'>
                                    <?php
                                    if(Yii::$app->user->can('cargarAlumno'))
                                        echo Html::a('<i class="fa fa-pencil"></i>', ['empadronamiento', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
                                    <?php 
                                    if(Yii::$app->user->can('eliminarAlumno'))
                                        echo Html::a('<i class="fa fa-trash-o"></i>', 'javascript:void(0)', [
                                            'data-url' =>  Url::to(['delete', 'id' => $model->id]),
                                            'class' => 'btn btn-danger btn-delete-alumno'
                                        ]); ?>
                                    
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                  
                    <div class="col-sm-12">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <i class="fa fa-user-plus"></i> <h3 class="box-title"> Bonificaciones </h3> 
                            <span class="pull-right">
                                <?php
                                    if(Yii::$app->user->can('gestionarBonificacionAlumno')){
                                        echo Html::button('<i class="fa fa-share-square-o"></i>',
                                                ['value'=> Url::to(['/alumno/asignar-bonificacion', 'alumno' => $model->id]), 
                                                    'class' => 'btn btn-xs btn-warning','id'=>'btn-asignar-bonificacion']);    
                                    }
                                    ?>

                            </span>
                        </div>
                        <div class="box-body">
                            <?php Pjax::begin(
                                    [
                                    'id'=>'pjax-bonificaciones',
                                    'enablePushState' => false,
                                    'timeout'=>false,                                    
                                    ]); ?>    
                            
                            <?= GridView::widget([
                                'id'=>'grid-bonificaciones',
                                'summary'=>'',
                                'dataProvider' => $misBonificaciones,            
                                'columns' => [  
                                    [
                                        'label' => 'Bonificacion',
                                        'value' => function($model) {
                                            return $model->bonificacion->descripcion;
                                        },
                                    ],
                                    [
                                        'label' => 'Valor',                                   
                                        'value' => function($model) {
                                            return $model->bonificacion->valor;
                                        },
                                    ],                            
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'template'=>'{quitar}',
                                        'buttons' => 
                                        [
                                        'quitar' => function ($url, $model) {                                
                                            return Html::a( '<i class="glyphicon glyphicon-trash"></i>',
                                               ['alumno/quitar-bonificacion', 'id'=>$model['id']],
                                               ['class'=>'btn btn-xs btn-danger',
                                                'onclick'=>'js:{quitarBonificacion("'.Url::to(['alumno/quitar-bonificacion', 'id'=>$model['id']]) .'","#pjax-bonificaciones"); return false;}']
                                            );
                                        },
                                        ],
                                        'visible'=>Yii::$app->user->can('gestionarBonificacionAlumno'),
                                    ],
                                ],
                            ]); ?>
                            <?php Pjax::end(); ?>  
                        
                        </div>
                    </div> <!-- box-bonificacione -->
                    
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <i class="fa fa-user-plus"></i> <h3 class="box-title"> Servicios Otorgados </h3> 
                        </div>
                        <div class="box-body">
                            <?= app\widgets\buscadorServiciosAlumno\BuscadorServiciosAlumno::widget(
                                    ['searchModel' => $searchMisServicios,
                                        'dataProvider'=>$misServicios,
                                        'buscador'=>true,
                                        'notDisplayColumn'=>['alumno'],
                                        'notDisplaySearch'=>['familia','alumno','establecimiento']
                                    ]);
                            ?>
                        </div>
                    </div> <!-- box-Servicios Alumno -->
                </div>              
            </div>
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
                intro: 'Información - Gestion del Alumno. '
            },  
            {
                element: document.querySelector('#tabledataalumno'),
                intro: 'Información.'
            }, 
            {
                element: document.querySelector('#bot-gestionalumno'),
                intro: 'Gestionar gestionar alumno.'
            },          
            
            {
                element: document.querySelector('#btn-asignar-bonificacion'),
                intro: 'Asignar bonificación.'
            },
            
        ]
      });
      intro.start();
}
  
", \yii\web\View::POS_END,'ayuda');
?>