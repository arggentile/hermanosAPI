<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TipoMovimientoCuenta */

$this->title = 'Create Tipo Movimiento Cuenta';
$this->params['breadcrumbs'][] = ['label' => 'Tipo Movimiento Cuentas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipo-movimiento-cuenta-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
