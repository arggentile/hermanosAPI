<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BonificacionAlumno */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bonificacion-alumno-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_bonificacion')->textInput() ?>

    <?= $form->field($model, 'id_alumno')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
