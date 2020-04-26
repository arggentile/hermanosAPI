<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ServicioDebitoAutomatico */

$this->title = 'Update Servicio Debito Automatico: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Servicio Debito Automaticos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="servicio-debito-automatico-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
