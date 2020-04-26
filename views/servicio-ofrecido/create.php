<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ServicioOfrecido */

$this->title = 'Alta Servicio Ofrecido';
$this->params['breadcrumbs'][] = ['label' => 'Servicio Ofrecidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-default  box-colegio">
    <div class="box-header with-border">
        <i class="fa fa-cogs"></i> <h3 class="box-title"> Alta Servicio </h3> 
    </div>
    <div class="box-body">    

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>
</div>