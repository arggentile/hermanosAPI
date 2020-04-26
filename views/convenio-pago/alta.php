<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
?>

<?php $this->title = 'Convenios Pagos';?>
<div class="box box-default box-colegio">
    <div class="box-header with-border">
        <i class="fa fa-handshake-o fa-2"></i> <h3 class="box-title"> Alta Convenio Pago - Selección Familia </h3>
    </div>
    <div class="box-body">

    <?php echo $this->render('_searchAlta',['modelGrupoFamiliar'=>$modelGrupoFamiliar]); ?>  

    <?php
    Pjax::begin(['id'=>'pjax-clientes-convenio']); 
    echo GridView::widget([
        'id'=>'grid-clientes-convenio',
        'dataProvider' => $dataClientes,                
        'columns' => [           
            [
                'label' => 'Apellidos',
                'attribute'=>'apellidos',
                'value' => function($model) {
                    return $model->apellidos;
                },
            ],
            [
                'label' => 'Folio',
                'attribute'=>'folio',
                'value' => function($model) {
                    return $model->folio;
                },
            ],
            [
                'label' => 'Responsable',
                'attribute'=>'responsable',
                'value' => function($model) {
                    return $model->miResponsableCabecera;
                },
            ],         
            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{alta-cp}',
                'buttons' => 
                [
                'alta-cp' => function ($url, $model) {                                
                    return Html::a( '<i class="glyphicon glyphicon-ok-circle"></i>',
                                           ['/convenio-pago/alta-servicios', 'familia'=>$model->id],
                                           ['title'=>'Generar Plan Pago']
                                );
                    },  
                ]   
            ],
        ],
    ]); 
    Pjax::end();
    ?>
        
    </div>
</div>

<style type="text/css">
    .table>tbody>tr:hover>td{
        background-color: rgba(155, 155, 169, 0.2) !important;
        cursor: pointer;
    }
</style>

