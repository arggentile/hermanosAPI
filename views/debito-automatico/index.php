<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\DebitoAutomaticoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Debitos Automaticos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-colegio" id="debito-automatico-index">
    <div class="box-header with-border">
        <i class="fa  fa-user-plus"></i><h3 class="box-title"> Debitos Automáticos </h3>
            <div class="box-tools pull-right">
                
                <?php
                if(Yii::$app->user->can('altaDebitoAutomatico'))
                    echo Html::a('<i class=\'fa fa-plus-square\'></i> Nuevo', ['/debito-automatico/alta'], ['class' => 'btn btn-success btn-xs','id'=>'btn-alta']) ?>
            </div>
    </div>
    <div class="box-body">        
        
        <?= $this->render('_search',['model'=>$searchModel, 'filter'=> $filter ]);?>
         
        <div class='table-responsive'>
            
            
            <?php Pjax::begin(
            [
                'id'=>'pjax-debitosautomaticos',                       
                'enablePushState' => false,
                'timeout'=>false,
            ]); ?>    
            
            <?= GridView::widget([
                'id'=>'grid-debitosautomaticos',
                'dataProvider' => $dataProvider,                
                'columns' => [
                    'id',
                    [
                        'label' => 'Tipo',
                        'attribute'=>'tipo_archivo',
                        'value' => function($model) {
                            if($model->tipo_archivo== app\models\DebitoAutomatico::ID_TIPODEBITO_CBU)
                                        return "Debito x CBU";
                                    else
                                        return "Debito x TC";
                                    
                                       
                           
                        },
                    ],                    
                    'nombre',
                    [
                        'label' => 'Reg.Enviados',
                        'attribute'=>'registros_enviados',
                        'value' => function($model) {
                            if(empty($model->registros_enviados))
                                return 0;
                            else
                                return $model->registros_enviados;
                        },
                        'contentOptions' =>['class'=>'registros-debito'],
                    ],    
                    [
                        'label' => 'Reg.Correctos',
                        'attribute'=>'registros_correctos',
                        'value' => function($model) {
                            if(empty($model->registros_correctos))
                                return 0;
                            else
                                return $model->registros_correctos;                            
                        },
                        'contentOptions' =>['class'=>'registros-debito'],
                    ],
                    [
                        'label' => 'Imp.Entrante',
                        'attribute'=>'saldo_entrante',
                        'value' => function($model) {
                            if(empty($model->saldo_entrante))
                                return 0;
                            else
                                return $model->saldo_entrante;                            
                        },
                        'contentOptions' =>['class'=>'importe'],
                    ],                  
                                
                    [
                        'label' => 'Procesado',
                        'attribute'=>'procesado',
                        'value' => function($model) {
                            if($model->procesado=='0')
                                return "No";
                            else
                                return "Si";
                        },
                        'contentOptions' =>['class'=>'procesado'],

                    ],                
                    ['class' => 'yii\grid\ActionColumn',
                     'template'=>'{view}',   
                    ],
                ],
            ]); ?>
            <?php Pjax::end(); ?>
                </div>
            
</div>
    
    <?php
$this->registerJs("
    $('#form-search-debitosautomaticos').on('beforeSubmit', function (e) {     
        e.preventDefault();
        
        dataOptionPjax = 
        {
            url: '" . \yii\helpers\Url::current() ."',
            container: '#pjax-debitosautomaticos', 
            timeout: false,
            data: $('#form-search-debitosautomaticos').serialize()
        };
            
        $.pjax.reload(dataOptionPjax);  
        return false;
    }); 
    
    $('#form-search-debitosautomaticos .btn-export-listado').click(function(){
        var curr_page = window.location.href;
        if (curr_page.indexOf('?') !== -1)
            var curr_page =  curr_page +  '&export=1';
        else
            var curr_page = curr_page +  '?export=1';
        window.open(curr_page,'_blank');       
    });
   
    
", \yii\web\View::POS_READY);
?>
    
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
                element: document.querySelector('.box-header'),
                intro: "Administración de Débito Automático. "
            },  
            { 
                element: document.querySelector('#grid-debitosautomaticos'),
                intro: "Listado de débitos automáticos."
            },
            {
                element: document.querySelector('.grid-view .filters'),
                intro: "Filtros para realizar busquedas específicas."
            },
            {
                element: document.querySelector('#btn-alta'),
                intro: "Si deséa realizar una nueva alta."
            },
        ]
      });
      intro.start();
}      
</script>
<style type="text/css">
    .registros-debito{
        width: 110px;
    }
    .procesado{
        width: 75px;
    }
    .importe{
        width: 130px;
    }
</style>