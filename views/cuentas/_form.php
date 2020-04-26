<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Cuentas */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cuentas-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fecha_apertura')->textInput() ?>

    <?= $form->field($model, 'saldo_inicial')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'saldo_actual')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
