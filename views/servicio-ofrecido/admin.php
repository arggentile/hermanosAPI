<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ServicioOfrecidoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Servicios Ofrecidos';
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="tipo-documento-index" class="box box-colegio">
    <div class="box-header with-border">
        <i class="fa fa-cogs"></i> <h3 class="box-title"> Administración Servicios </h3> 
        <p class="pull-right"> 
                <?=  Html::a('<i class="glyphicon glyphicon-plus"></i> Alta Servicio', 
                        ['alta'], ['class' => 'btn btn-xs btn-select btn-alta']); ?> 
            </p>
    </div>
    <div class="box-body"> 
            <?= $this->render('_searchAdmin',['model'=>$searchModel,  'filtros'=> $data['filtros']]);?>
            
            
                    
            <?php Pjax::begin([
                'id'=>'pjax-servicioofrecido',
                'enablePushState' => false,
                'timeout'=>false,
            ]); ?>    
                
            <?= GridView::widget([
                'id'=>'grid-serviciosofrecidos',
                'dataProvider' => $dataProvider, 
                'rowOptions' => function ($model, $index, $widget, $grid){
                    if($model->activo == '0'){
                      return ['class' => 'servicioinactivo'];
                    }
                  },
                'columns' => [                    
                    [
                        'label' => 'Servicio',
                        'attribute'=>'id_categoriaservicio',
                        'filter'=> dmstr\helpers\Html::activeDropDownList($searchModel, 'id_categoriaservicio', app\models\CategoriaServicioOfrecido::getTipoServicios(), ['prompt'=>'TODOS', 'class'=>'form-control']),
                        'value' => function($model, $key, $index, $column) {
                                return $model->miTiposervicio->descripcion;
                        },
                    ],
                    'nombre',                    
                    [
                        'label' => 'Importe',
                        'value' => function($model, $key, $index, $column) {
                                return "$ " . $model->importe;
                            },
                    ],
                    [
                        'label' => 'Importe H.P',
                        'value' => function($model, $key, $index, $column) {
                                return "$ " . $model->importe_hijoprofesor;
                            },
                    ],   
                    [
                        'label' => 'Periodo',
                        'value' => function($model, $key, $index, $column) {
                                return $model->getDetallePeriodo();
                            },                        
                    ],    
                    [
                        'label' => 'Vencimiento',
                        'value' => 'fecha_vencimiento',
                        'value' => function($model, $key, $index, $column) {
                                return $model->xfecha_vencimiento;
                            },                        
                    ],  
                    [
                        'label' => 'Devenga',
                        'attribute'=>'devengamiento_automatico',
                        'filter'=> dmstr\helpers\Html::activeDropDownList($searchModel, 'devengamiento_automatico',['0'=>'NO','1'=>'SI'] ,['prompt'=>'-','class'=>'form-control']),
                        'value' => function($model, $key, $index, $column) {
                            if($model->devengamiento_automatico=='0')
                                return "No";
                            else
                                return "Si";                            
                        },
                    ],
                    [
                        'label' => 'Activo',
                        'attribute'=>'activo',
                        'filter'=> dmstr\helpers\Html::activeDropDownList($searchModel, 'activo',['0'=>'NO','1'=>'SI'] ,['prompt'=>'-','class'=>'form-control']),
                        'value' => function($model, $key, $index, $column) {
                            if($model->activo=='0')
                                return "No";
                            else
                                return "Si";                            
                        },
                    ],

                    [
                    'class' => 'yii\grid\ActionColumn',
                    'headerOptions' => ['width' => '50', 'class'=>'actionsgrid'],
                    'template'=>'{update} {view}',
                    'visibleButtons' => [                                   
                                'update' => Yii::$app->user->can('gestionarServicios'),
                                'view' =>Yii::$app->user->can('gestionarServicios'),
                            ],
                    ],
                ],
            ]); 
            ?>
        <?php Pjax::end(); ?>   
           
    </div>
</div>

<input type="hidden" name="url-reload-listado-serviciosofrecidos" id="url-reload-listado-serviciosofrecidos" value="<?= yii\helpers\Url::current(); ?>">
<style type="text/css">
    .servicioinactivo{background-color: #F0D595 !important;}
</style>
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
                intro: "Listado de Servicios Ofrecidos. "
            },  
            {
                element: document.querySelector('.servicio-ofrecido-search'),
                intro: "Filtros para realizar busquedas especificas, puede especificar más de un dato."
            },            
            {
                element: document.querySelector('.grid-view tbody'),
                intro: "El resultado de la busqueda sera desplegado en esta sección."
            },
            {
                element: document.querySelector('.btn-export'),
                intro: "Si desea exportar el listado, presione el boton."
            },
            {
                element: document.querySelector('.btn-alta'),
                intro: "Si desea realizar una nueva alta, presione sobre este aqui."
            },
        ]
      });
      intro.start();
}      
</script>
<?php 
    $this->registerJsFile('@web/js/servicio-ofrecido.js', ['depends'=>[app\assets\AppAsset::className()]]);
?>