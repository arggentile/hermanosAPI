<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ServicioConvenioPago */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="servicio-convenio-pago-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_conveniopago')->textInput() ?>

    <?= $form->field($model, 'id_servicio')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
