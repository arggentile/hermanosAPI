<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Establecimiento */

$this->title = 'Alta Establecimiento';
$this->params['breadcrumbs'][] = ['label' => 'Establecimientos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-default box-colegio establecimiento-create">
    <div class="box-header with-border">
        <i class="fa fa-university"></i> <h3 class="box-title"> Alta Establecimiento </h3>
    </div>
    <div class="box-body">
        <?= $this->render('_formEstablecimiento', [
            'model' => $model,
        ]) ?>
    </div>
</div>