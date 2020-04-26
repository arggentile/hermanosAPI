<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ServiciosTiket */

$this->title = 'Update Servicios Tiket: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Servicios Tikets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="servicios-tiket-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
