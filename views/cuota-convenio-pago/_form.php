<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CuotaConvenioPago */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cuota-convenio-pago-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_conveniopago')->textInput() ?>

    <?= $form->field($model, 'fecha_establecida')->textInput() ?>

    <?= $form->field($model, 'nro_cuota')->textInput() ?>

    <?= $form->field($model, 'monto')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'estado')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_tiket')->textInput() ?>

    <?= $form->field($model, 'importe_abonado')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
