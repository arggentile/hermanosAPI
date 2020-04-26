<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MovimientoCuenta */

$this->title = 'Update Movimiento Cuenta: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Movimiento Cuentas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="movimiento-cuenta-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
