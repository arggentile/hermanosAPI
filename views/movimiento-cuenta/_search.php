<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\MovimientoCuenta */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="movimiento-cuenta-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_cuenta') ?>

    <?= $form->field($model, 'id_tipo_movimiento') ?>

    <?= $form->field($model, 'detalle_movimiento') ?>

    <?= $form->field($model, 'importe') ?>

    <?php // echo $form->field($model, 'fecha_realizacion') ?>

    <?php // echo $form->field($model, 'comentario') ?>

    <?php // echo $form->field($model, 'id_tipopago') ?>

    <?php // echo $form->field($model, 'id_hijo') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
