<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\HistoriaEgresosAlumno */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="historia-egresos-alumno-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_alumno')->textInput() ?>

    <?= $form->field($model, 'id_division_actual')->textInput() ?>

    <?= $form->field($model, 'id_division_egreso')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
