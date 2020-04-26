<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\DivisionEscolar */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Division Escolars';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
    Pjax::begin([
        'id' => 'pjax-divisionesservicios',
        'class' => 'pjax-loading',
        'enablePushState' => false,
        'timeout' => false,
    ]);
?>   

    <?=
    GridView::widget([
        'id' => 'grid-divisiones',
        'dataProvider' => $dataProviderDivisiones,
        'columns' => [
           
            'nombre',
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{asignar}{quitar}',
                'headerOptions' => ['class' => 'btnquitar'],
                'buttons' =>
                [
                'asignar' => function ($url, $model) use ($modelServicio, $divisionesConServicio){
                    if(array_key_exists($model->id, $divisionesConServicio)==FALSE)
                        return Html::button( '<i class="glyphicon glyphicon-ok-circle"></i>',                            
                            ['class' => 'btn btn-xs btn-primary',
                                'title'=>'Asignar Servicio-Division',
                                'onclick' => 'js:asignarServicioDivision("'.Url::to(['/establecimiento/asignar-servicio-division', 'division' => $model->id, 'servicio' => $modelServicio->id]) .'");']
                            );
                        },
                'quitar' => function ($url, $model) use ($modelServicio, $divisionesConServicio){
                    if(array_key_exists($model->id, $divisionesConServicio))        
                        return Html::button( '<i class="glyphicon glyphicon-remove-circle"></i>',                            
                            [    'class' => 'btn btn-xs btn-danger',
                                'title'=>'Remover Servicio-Division',
                                'onclick' => 'js:quitarServicioDivision("'.Url::to(['/establecimiento/quitar-servicio-division', 'division' => $model->id, 'servicio' => $modelServicio->id]) .'");']
                            );
                        },
                ],
            ],
        ],
    ]);
    ?>
<?php Pjax::end(); ?>

<input type="hidden" name="urlreloadservicios" id="urlreloadservicios" value="<?= Url::to(['/establecimiento/get-servicios','idEstablecimiento'=>$modelEstablecimiento->id,'idServicio'=>$modelServicio->id]); ?>" />

<?php 
$this->registerJsFile('@web/js/servicios-establecimiento.js', ['depends'=>[app\assets\AppAsset::className()]]);
?>