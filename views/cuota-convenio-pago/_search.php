<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\CuotaConvenioPagoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cuota-convenio-pago-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_conveniopago') ?>

    <?= $form->field($model, 'fecha_establecida') ?>

    <?= $form->field($model, 'nro_cuota') ?>

    <?= $form->field($model, 'monto') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'id_tiket') ?>

    <?php // echo $form->field($model, 'importe_abonado') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
