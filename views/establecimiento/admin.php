<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EstablecimientoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Establecimientos';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-colegio">
    <div class="box-header with-border">
        <i class="fa fa-university"></i> <h3 class="box-title"> Establecimientos </h3>  
        <p class="pull-right">
            <?php
            if(Yii::$app->user->can('cargarEstablecimiento'))
                echo Html::a('<i class="glyphicon glyphicon-plus"></i> Alta', ['alta'], ['class' => 'btn btn-xs btn-success btn-alta']) ?>
          </p>
    </div>
    <div class="box-body">         
        
          
        
                <?php Pjax::begin([
                    'id'=>'pjax-establecimientos',
                    'class'=>'pjax-loading',
                    'enablePushState' => false,
                    'timeout'=>false,                    
                    ]); ?>
                
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        'nombre',                        
                        'calle',
                        'telefono',                        
                        'mail',
                        'nivel_educativo',
                        ['class' => 'yii\grid\ActionColumn', 
                         
                         'template'=>'{view} {update}',
                         'visibleButtons' => [                                   
                                    'view' => Yii::$app->user->can('visualizarEstablecimiento'),
                                    'update' =>Yii::$app->user->can('cargarEstablecimiento'),
                                ],  
                         'headerOptions' => ['width' => '50','class'=>''], 
                         'contentOptions' => ['width' => '50','class'=>'opcionesgrid'], 
                            
                        ],
                    ],
                ]); ?>
                <?php Pjax::end(); ?>   
            
    </div>    
</div>
<script type="text/javascript">
function ayuda(){         
    var intro = introJs();
      intro.setOptions({
        nextLabel: 'Siguiente',
        prevLabel: 'Anterior',
        skipLabel:'Terminar',
        doneLabel:'Cerrar',
        steps: [   
            {
                element: document.querySelector('.grid-view tbody'),
                intro: "Listado de Establecimientos."
            },
            {
                element: document.querySelector('.opcionesgrid'),
                intro: "Gestion establecimiento."
            },
            {
                element: document.querySelector('.btn-alta'),
                intro: "Si desea realizar una nueva alta, presione sobre este botón."
            },
        ]
      });
      intro.start();
}      
</script>