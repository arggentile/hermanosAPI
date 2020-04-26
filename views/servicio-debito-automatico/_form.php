<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ServicioDebitoAutomatico */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="servicio-debito-automatico-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_debitoautomatico')->textInput() ?>

    <?= $form->field($model, 'id_servicio')->textInput() ?>

    <?= $form->field($model, 'tiposervicio')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'resultado_procesamiento')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'linea')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
