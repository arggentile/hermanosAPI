<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\ServicioDebitoAutomatico */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="servicio-debito-automatico-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_debitoautomatico') ?>

    <?= $form->field($model, 'id_servicio') ?>

    <?= $form->field($model, 'tiposervicio') ?>

    <?= $form->field($model, 'resultado_procesamiento') ?>

    <?php // echo $form->field($model, 'linea') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
