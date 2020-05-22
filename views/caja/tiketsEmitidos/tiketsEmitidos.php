<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

use app\assets\AlumnoAssets;
AlumnoAssets::register($this);

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AlumnoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reporte Tikets';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box  box-colegio alumno-index">
    <div class="box-header with-border">
        <i class="fa fa-users"></i> <h3 class="box-title"> Reporte Tikets </h3>    
    </div>
    <div class="box-body">
            <?= $this->render('_search',[
                'searchModelTiket'=>$searchModelTiket, 
            ]);?>

       
        <?php Pjax::begin([
            'id'=>'pjax-tikets-emitidos',                
            'timeout'=>false,
            ]); ?>    
              
        <?= GridView::widget([
            'id'=>'grid-tikets-emitidos',
            'dataProvider' => $dataProviderTiketEmitidos,               
            'columns' => [
                [
                    'label' => 'Nro',
                    'attribute'=>'id',                         
                    'value' => function($model) {
                        return $model->id;
                    },
                ],
                [
                    'label' => 'Fecha',
                    'attribute'=>'fecha_tiket',                         
                    'value' => function($model) {
                        return $model->xfecha_tiket;
                    },
                ],
                [
                    'label' => 'Forma Pago',
                    'attribute'=>'id_tipopago',                         
                    'value' => function($model) {
                        return $model->tipopago->nombre;
                    },
                ],            
                [
                    'label' => 'Importe',
                    'attribute'=>'importe',                         
                    'value' => function($model) {
                        return $model->importe;
                    },
                ],
                [
                    'label' => 'Factura',
                    'value' => function($model) {
                        if($model->miFactura && ($model->miFactura->informada || !empty($model->miFactura->cae)))
                            return "SI: ". $model->miFactura->fecha_informada;
                        else
                            return "No";
                    },
                ],
                [
                    'label' => 'CAE',
                    'value' => function($model) {
                        if($model->miFactura && ($model->miFactura->informada || !empty($model->miFactura->cae)))
                            return $model->miFactura->cae;
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'headerOptions' => ['width' => '50','class'=>'actionsgrid'],
                    'template'=>'{view} {pdf} ',
                    'buttons' => 
                    [
                        'view' => function ($url, $model) {                                
                                    return Html::a('<i class="fa fa-eye"></i>',
                                                           ['/caja/detalle-tiket', 'id'=>$model['id']],
                                                           ['class'=>'','title'=>'Visualizar Detalle Tiket']
                                                   );
                            },
                        'pdf' => function ($url, $model) {                                
                                    return Html::a( '<i class="glyphicon glyphicon-pencil"></i>',
                                                           ['/caja/pdf-tiket', 'id'=>$model['id']],
                                                           ['class'=>'','title'=>'Imprimir Factura']
                                                   );
                            },            
                    ],
                    //'visible'=>Yii::$app->user->can('cargarAlumno')||Yii::$app->user->can('visualizarAlumno'),
                ],            
                ],
            ]); ?>
        <?php Pjax::end(); ?>
         
    </div>
</div>

<?php
$this->registerJs("
    $('#form-reporte-tikets').on('beforeSubmit', function (e) {     
        e.preventDefault();
        
        dataOptionPjax = 
        {
            url: '" . Url::current() ."',
            container: '#pjax-tikets-emitidos', 
            timeout: false,
            data: $('#form-reporte-tikets').serialize()
        };
            
        $.pjax.reload(dataOptionPjax);  
        return false;
    });    

    $('#form-reporte-tikets .btn-export-listado').click(function(){
        var curr_page = window.location.href;
        if (curr_page.indexOf('?') !== -1)
            var curr_page =  curr_page +  '&export=1';
        else
            var curr_page = curr_page +  '?export=1';
        window.open(curr_page,'_blank');       
    });
    
", \yii\web\View::POS_READY);
?>