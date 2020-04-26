<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-warning alumnos-establecimiento">
            <div class="box-header with-border">
            </div>
            <div class="box-body">
                <div class="pull-right">
                    <p>                   
                        <?php
                        if(Yii::$app->user->can('cargarAlumno'))
                            echo Html::a('<i class="fa fa-plus-square"></i> Cargar', ['/alumno/empadronamiento'], ['class' => 'btn btn-primary btn-xs']);
                        ?>
                        <?php
                        if(Yii::$app->user->can('exportarAlumno'))
                            echo Html::a('<i class="glyphicon glyphicon-download"> </i> Exportar', 
                                    'javascript:void(0)',
                                    ['data-url'=> Url::to(['/alumno/listado']) ,'target'=>'_blank', 'class' => 'btn btn-success btn-xs btn-export-alumno-esxtablecimiento'])
                        ?>
                    </p>
                </div>
                <div>
                    <?php
                    Pjax::begin([
                        'id' => 'pjax-alumnos',                       
                        'enablePushState' => false,
                        'timeout' => false,
                    ]);
                    ?>    
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProviderAlumnos,
                        'filterModel' => $searchModelAlumnos,
                        'columns' => [
                            'nro_legajo',
                            [
                                'label' => 'Documento',
                                'attribute' => 'documento',
                                'filter' => dmstr\helpers\Html::activeInput('text', $modelPersona, 'nro_documento', ['class' => 'form-control']),
                                'value' => function($model) {
                                    return $model->miPersona->nro_documento;
                            },
                            ],
                            [
                                'label' => 'Apellido',
                                'attribute' => 'apellido',
                                'filter' => dmstr\helpers\Html::activeInput('text', $modelPersona, 'apellido', ['class' => 'form-control']),
                                'value' => function($model) {
                                    return $model->miPersona->apellido;
                            },
                            ],
                            [
                                'label' => 'Nombre',
                                'attribute' => 'nombre',
                                'filter' => dmstr\helpers\Html::activeInput('text', $modelPersona, 'nombre', ['class' => 'form-control']),
                                'value' => function($model) {
                                    return $model->miPersona->nombre;
                            },
                            ],
                            [
                                'label' => 'Familia',
                                'attribute' => 'familia',
                                'value' => function($model) {
                                    return $model->miGrupofamiliar->apellidos;
                            },
                            ],
                            [
                                'label' => 'División',
                                'attribute' => 'id_divisionescolar',
                                'filter' => dmstr\helpers\Html::activeDropDownList($searchModelAlumnos, 'id_divisionescolar', \app\models\DivisionEscolar::getDivisionesEstablecimiento($modelEstablecimiento->id), ['prompt' => '', 'class' => 'form-control']),
                                'value' => function($model) {
                                    return $model->divisionescolar->nombre;
                            },
                            ],
                            ['class' => 'yii\grid\ActionColumn',
                                'header' => '-',
                                'headerOptions' => ['style' => 'color:#337ab7'],
                                'template' => '{view}',
                                'visibleButtons' => [                                   
                                    'view' =>Yii::$app->user->can('visualizarAlumno'),
                                ],
                                'buttons' => [
                                    'view' => function ($url, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['alumno/view','id'=>$model->id]), ['title' => Yii::t('app', 'DETALLES ALUMNO')]);
                                    },
                                ],'visible'=>Yii::$app->user->can('visualizarAlumno'),
                            ],
                            ],
                                    ]);
                    ?>
                    <?php Pjax::end(); ?>
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
            {
                element: document.querySelector('.alumnos-establecimiento'),
                intro: 'Listado de alumno pertenecientes al establecimiento.'
            },
        ]
      });
      intro.start();
} 
", \yii\web\View::POS_END,'ayuda');
?>