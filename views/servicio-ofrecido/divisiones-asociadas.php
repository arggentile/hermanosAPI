<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;

use app\widgets\buscadorServiciosAlumno;

/* @var $this yii\web\View */
/* @var $model app\models\ServicioOfrecido */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Servicio Ofrecidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-default box-colegio ">
    <div class="box-header with-border">
        <i class="fa fa-cogs"></i> <h3 class="box-title"> Detalle Servicio <b><?= $model->nombre;?> </b></h3> 
    </div>
    <div class="box-body">   

        <?= $this->render('_viewDataServicioOfrecido',['model'=>$model]); ?>
        <br />
        <div class="box box-primary box-colegio ">
            <div class="box-header with-border">
                <i class="fa fa-cogs"></i> <h3 class="box-title"> Divisiones Escolares Asociadas </h3> 
                <div class="pull-right">
                    <p>
                    <?=  Html::button('<i class="fa fa-plus-square"></i> Administrar Servicio', 
                            //['/establecimiento/nuevo-servicio','id_establecimiento'=>$modelEstablecimiento->id], 
                            ['class' => 'btn btn-default btn-alta btn-asociardivision',
                              'data-xhref'=>\yii\helpers\Url::to(['/servicio-ofrecido/asociar-division','id'=>$model->id])]) ?>
                    </p>
                </div>
            </div>
            
            <div class="box-body"> 
                <?php
                Pjax::begin([
                    'id' => 'pjax-divisionesdelservicios',
                    'class' => 'pjax-loading',
                    'enablePushState' => false,
                    'timeout' => false,
                ]);
                ?>   
                <table id="" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th> Establecimiento </th>
                            <th> Divisón Escolar </th>                                             
                        </tr>                
                    </thead>
                    <tbody>
                        <?php
                        if(!empty($modeDivisionesAsociadas)){
                        foreach ($modeDivisionesAsociadas as $divEstablecimiento){?>
                        <tr>
                            <td><?=  $divEstablecimiento->divisionescolar->establecimiento->nombre; ?> </td>
                            <td>   <?=  $divEstablecimiento->divisionescolar->nombre ;?>  </td>                                      
                        </tr>
                        <?php }} ?>
                    </tbody>
                </table>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
    yii\bootstrap\Modal::begin([        
        'id'=>'modal-divisiones-servicio',
        'header' => 'Asociación de Divisiones',
        'options' => ['class' =>'modal-scrollbar modalSite'],
                
    ]);
        echo "<div class='micontent'></div>";
    yii\bootstrap\Modal::end();
?>
<?php 
    $this->registerJsFile('@web/js/servicio-ofrecido.js', ['depends'=>[app\assets\AppAsset::className()]]);
?>