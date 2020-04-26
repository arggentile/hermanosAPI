<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\ServicioAlumnoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="servicio-alumno-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_servicio') ?>

    <?= $form->field($model, 'id_alumno') ?>

    <?= $form->field($model, 'fecha_otorgamiento') ?>

    <?= $form->field($model, 'fecha_cancelamiento') ?>

    <?php // echo $form->field($model, 'importe_servicio') ?>

    <?php // echo $form->field($model, 'importe_descuento') ?>

    <?php // echo $form->field($model, 'importe_abonado') ?>

    <?php // echo $form->field($model, 'id_estado') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
