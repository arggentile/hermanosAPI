<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;


use app\assets\AlumnoAssets;
AlumnoAssets::register($this); 

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\BonificacionAlumno */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bonificacion Alumnos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box  box-colegio bonificacion-index">
    <div class="box-header with-border">
        <i class="fa fa-users"></i> 
        <h3 class="box-title"> Alumnos con Bonificaciones </h3>    
    </div>
    <div class="box-body">
        <?= $this->render('_search',['modelPersona'=>$modelPersona,]);?>        

        <?php Pjax::begin( [
            'id'=>'pjax-bonificaciones',
            'enablePushState' => false,
            'timeout'=>false,                                    
            ]); ?>    
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,           
            'columns' => [
                    [
                        'label' => 'Bonificacion',
                        'attribute' => 'id_bonificacion',
                        //'filter' => dmstr\helpers\Html::activeDropDownList($searchModel, 'id_bonificacion', \app\models\CategoriaBonificacion::getBonificaciones(), ['prompt' => '', 'class' => 'form-control']),
                        'value' => function($model) {
                            return $model->bonificacion->descripcion . " " . $model->bonificacion->valor;
                        },
                    ],
                    [
                        'label' => 'Documento',
                        'attribute' => 'documento_alumno',
                        'value' => function($model) {
                            return $model->alumno->persona->nro_documento;
                        },
                    ],        
                    [
                        'label' => 'Alumno',   
                        'format'=>'raw',
                        'value' => function($model) {
                            return Html::a("<i class='fa fa-eye'></i> " . $model->alumno->persona->apellido. ", ".$model->alumno->persona->nombre,
                                    Url::to(['/alumno/view', 'id'=>$model->alumno->id]));
                        },
                    ],
                    [
                        'label' => 'Familia',
                        'attribute' => 'familia_alumno',
                        'format' => 'raw',
                        // 'filter' => dmstr\helpers\Html::activeInput('text', $modelPersona, 'nro_documento', ['class' => 'form-control']),
                        'value' => function($model) {
                            return Html::a("<i class='fa fa-eye'></i> " .  $model->alumno->grupofamiliar->apellidos . "<span class='text text-danger'> Folio:" . $model->alumno->grupofamiliar->folio . "</span>",
                                    Url::to(['/grupo-familiar/view','id'=>$model->alumno->grupofamiliar->id]));
                        }
                            
                    ],
                    [
                    'class' => 'yii\grid\ActionColumn',
                    'headerOptions' => ['width' => '50', 'class' => 'actionsgrid'],
                    'template' => '{removerbonificacion}',
                    'visible' => Yii::$app->user->can('gestionarBonificacionAlumno'),                      
                    'buttons' =>
                                    [
                                    'removerbonificacion' => function ($url, $model) {                                
                                        return Html::a( '<i class="fa fa fa-remove"></i>',
                                           ['alumno/quitar-bonificacion', 'id'=>$model['id']],
                                           ['class'=>'btn btn-xs btn-danger',
                                            'onclick'=>'js:{quitarBonificacion("'.Url::to(['alumno/quitar-bonificacion', 'id'=>$model['id']]) .'","#pjax-bonificaciones"); return false;}']
                                        );
                                    },
                                    ],    
                ],
            ],
        ]);
        ?>
        <?php Pjax::end(); ?>
    </div>
</div>



<?php
$this->registerJs("
    $('#form-search-bonificacionesalumnos').on('beforeSubmit', function (e) {     
        e.preventDefault();
        
        dataOptionPjax = 
        {
            url: '" . Url::current() ."',
            container: '#pjax-bonificaciones', 
            timeout: false,
            data: $('#form-search-bonificacionesalumnos').serialize()
        };
            
        $.pjax.reload(dataOptionPjax);  
        return false;
    });
    

    $('#form-search-bonificacionesalumnos .btn-export-listado').click(function(){
        var curr_page = window.location.href;  
        if (curr_page.indexOf('?') !== -1)
            var curr_page =  curr_page +  '&export=1';
        else
            var curr_page = curr_page +  '?export=1';
        window.open(curr_page, '_blank');                
    });   
    
", \yii\web\View::POS_READY);
?>



<script type="text/javascript">
    function ayuda() {
        var intro = introJs();
        intro.setOptions({
            nextLabel: 'Siguiente',
            prevLabel: 'Anterior',
            skipLabel: 'Terminar',
            doneLabel: 'Cerrar',
            steps: [
                {
                    element: document.querySelector('.box-header'),
                    intro: "Reporte de Alumnos cargados en el sistema a los cuales se les asignarion bonificaciones",
                },
                {
                    element: document.querySelector('.alumnosbonificaciones-search'),
                    intro: "Filtros para realizar busquedas especificas, puede especificar más de un dato."
                },
                {
                    element: document.querySelector('.grid-view tbody'),
                    intro: "El resultado de la busqueda sera desplegado en esta sección."
                },
                {
                    element: document.querySelector('.btn-export'),
                    intro: "Si deseamos exportar el resultado del listado, presionamos cobre el boton."
                },
            ]
        });
        intro.start();
    }
</script>