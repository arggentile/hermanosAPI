<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MovimientoCuenta */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="movimiento-cuenta-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_cuenta')->textInput() ?>

    <?= $form->field($model, 'id_tipo_movimiento')->textInput() ?>

    <?= $form->field($model, 'detalle_movimiento')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'importe')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fecha_realizacion')->textInput() ?>

    <?= $form->field($model, 'comentario')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_tipopago')->textInput() ?>

    <?= $form->field($model, 'id_hijo')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
