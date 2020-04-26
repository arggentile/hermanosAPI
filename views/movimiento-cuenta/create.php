<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MovimientoCuenta */

$this->title = 'Create Movimiento Cuenta';
$this->params['breadcrumbs'][] = ['label' => 'Movimiento Cuentas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movimiento-cuenta-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
