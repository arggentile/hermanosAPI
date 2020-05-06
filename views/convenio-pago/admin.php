<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ConvenioPagoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Convenios Pagos';
?>
<div class="box box-default box-colegio" id="convenio-pago-index">
    <div class="box-header with-border">        
        <i class="fa fa-handshake-o fa-2"></i> <h3 class="box-title"> Convenios Pago </h3> 
            <p class="pull-right">
                <?php
                if(Yii::$app->user->can('cargarConvenioPago'))
                    echo Html::a('<i class=\'fa fa-plus-square\'></i> Alta', ['alta'], ['class' => 'btn btn-primary btn-xs','id'=>'btn-alta']) ?>
            </p>
    </div>    
    <div class="box-body">
            <?= $this->render('_searchAdmin',['model'=>$searchModel]);?>
      
            <?php Pjax::begin([
                    'id'=>'pjax-convenios',                       
                    'enablePushState' => false,
                    'timeout'=>false,
                   ]); ?>    
            <?= GridView::widget([
                'id'=>'grid-convenios',
                'dataProvider' => $dataProvider,
                'columns' => [    
                    'id',            
                    'nombre',
                    'fecha_alta:date',
                    [
                        'label' => 'Familia',
                        'attribute'=>'id_familia',
                        'value' => function($model) {
                            return $model->miFamilia->apellidos ." - Folio". 
                                $model->miFamilia->folio;
                        },
                    ],
                    'saldo_pagar',
                    [
                        'label' => 'Deb.Aut',
                        'attribute'=>'deb_automatico',
                        'filter'=> dmstr\helpers\Html::activeDropDownList($searchModel,'deb_automatico',['0'=>'NO','1'=>'SI'],['prompt'=>'TODOS','class'=>'form-control']),
                        'value' => function($model) {
                            if($model->deb_automatico== '0')
                                return "NO";
                            else return "SI";
                        },
                    ],    
                    [
                        'label' => 'Cuotas',  
                        'format'=>'raw',
                        'contentOptions'=>['class'=>'columncenter'],
                        'value' => function($model) {
                            $return = "<span class='label label-success'>" . $model->cantCuotas."</span>";
                            if($model->cuotasPendientes==0)
                                $return .= " <span class='label label-warning'>AL DIA</span>";
                            else
                                $return .= " <span class='label label-warning' title='cuotas pendientes'>$model->cuotasPendientes pen</span>";

                            return $return;                
                        },
                    ],    
                       

                    ['class' => 'yii\grid\ActionColumn',
                     'template'=>' {view} {edit} {delete}',                     
                     'visibleButtons' =>
                        [
                            'delete' => Yii::$app->user->can('eliminarConvenioPago'),
                            'view' => Yii::$app->user->can('visualizarConvenioPago')
                        ],   
                     'buttons' => [
                        'edit' => function ($url, $model) {
                            if(Yii::$app->user->can('editarConvenioPago'))
                                if($model->sePuedeEditar())
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', 
                                        \yii\helpers\Url::to(['/convenio-pago/editar-plan-pago','id'=>$model->id]), 
                                        ['title' => 'Editar',]);
                            },
                        'delete' => function ($url, $model) {
                            if(Yii::$app->user->can('eliminarConvenioPago'))
                                if($model->sePuedeEditar())
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', 
                                        \yii\helpers\Url::to(['/convenio-pago/delete','id'=>$model->id]), 
                                        ['title' => 'Editar',]);
                            },            
                      ]              
                    ],
                ],
            ]); ?>
            <?php Pjax::end(); ?>    
    </div>
</div>

<?php
$this->registerJs("
    $('#form-search-conveniospago').on('beforeSubmit', function (e) {     
        e.preventDefault();
        
        dataOptionPjax = 
        {
            url: '" . \yii\helpers\Url::current() ."',
            container: '#pjax-convenios', 
            timeout: false,
            data: $('#form-search-conveniospago').serialize()
        };
            
        $.pjax.reload(dataOptionPjax);  
        return false;
    });
    
    $('#form-search-conveniospago .btn-export-listado').click(function(){
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
                intro: "Administración de Convenios de Pago. "
            },  
            { 
                element: document.querySelector('#grid-convenios'),
                intro: "Listado de convenios."
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