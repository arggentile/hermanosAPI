<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

use app\assets\CRUDAjaxAsset;
CRUDAjaxAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TipoDocumentoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gestión Categoriazación Servicios';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= \app\widgets\modalcrud\ModalCrud::widget(['titulo'=>'Alta/Actualización Categoria Servicios Ofrecidos']); ?>
<div id="tipo-documento-index">

    <div class="box box-default">
        <div class="box-header with-border">
            <i class="glyphicon glyphicon-wrench"></i> <h3 class="box-title"> Categoria Servicios Ofrecidos </h3> 
        </div>
        <div class="box-body">
            <p class="pull-right">
            <?=  Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success btn-alta btn-xs', 'data-title'=>'Alta',
                'onclick'=>'js:{cargaAjax("'.Url::to(['categoria-servicio-ofrecido/create']) .'"); return false;}']); ?>
            </p>
            <?php Pjax::begin(['id'=>'pjax-grid',
                ]); ?>   
            
            
            <?=   GridView::widget([
                'id'=>'grid_tsexos',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'descripcion',                       
                        ['class' => 'yii\grid\ActionColumn',
                            'template'=>'{update} {delete}',
                            'buttons' => 
                               [
                               'update' => function ($url, $model) {                                
                                                return Html::a( '<i class="glyphicon glyphicon-pencil"></i>',
                                                                       ['create', 'id'=>$model['id']],
                                                                       ['class'=>'btn btn-xs btn-primary editAjax',
                                                                        'onclick'=>'js:{editAjax("'.Url::to(['create', 'id'=>$model['id']]) .'"); return false;}']
                                                               );
                                       },
                               'delete' => function ($url, $model) {                                
                                               return Html::a( '<i class="glyphicon glyphicon-remove"></i>',
                                                                       ['delete', 'id'=>$model['id']],
                                                                       ['class'=>'btn btn-xs btn-danger deleteAjax',
                                                                        'onclick'=>'js:{deleteAjax("'.Url::to(['delete', 'id'=>$model['id']]) .'"); return false;}']
                                                               );
                                       },                

                               ]   
                        ],
                    ],
                ]); ?>
            <?php Pjax::end(); ?>
        </div>
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
                intro: "Administracion de categorización grupo servicios ofrecidos."
            },  
            {
                element: document.querySelector('.grid-view .filters'),
                intro: "Filtros para realizar busquedas especificas, puede especificar mas de un dato."
            },            
            {
                element: document.querySelector('.grid-view tbody'),
                intro: "El resultado de la busqueda sera desplegado en esta seccion."
            },
            {
                element: document.querySelector('.btn-alta'),
                intro: "Si desea realizar una nueva alta, presione sobre este boton."
            },
        ]
      });
      intro.start();
}      
</script>