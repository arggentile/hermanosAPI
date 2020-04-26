<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ServiciosTiket */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="servicios-tiket-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_tiket')->textInput() ?>

    <?= $form->field($model, 'id_servicio')->textInput() ?>

    <?= $form->field($model, 'tiposervicio')->textInput() ?>

    <?= $form->field($model, 'monto_abonado')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
