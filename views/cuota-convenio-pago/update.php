<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CuotaConvenioPago */

$this->title = 'Update Cuota Convenio Pago: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Cuota Convenio Pagos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cuota-convenio-pago-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
