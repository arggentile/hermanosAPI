<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\PersonaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="persona-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'apellido') ?>

    <?= $form->field($model, 'nombre') ?>

    <?= $form->field($model, 'fecha_nacimiento') ?>

    <?= $form->field($model, 'id_sexo') ?>

    <?php // echo $form->field($model, 'id_tipodocumento') ?>

    <?php // echo $form->field($model, 'nro_documento') ?>

    <?php // echo $form->field($model, 'calle') ?>

    <?php // echo $form->field($model, 'nro_calle') ?>

    <?php // echo $form->field($model, 'piso') ?>

    <?php // echo $form->field($model, 'dpto') ?>

    <?php // echo $form->field($model, 'localidad') ?>

    <?php // echo $form->field($model, 'telefono') ?>

    <?php // echo $form->field($model, 'celular') ?>

    <?php // echo $form->field($model, 'mail') ?>

    <?php // echo $form->field($model, 'grupo_sanguineo') ?>

    <?php // echo $form->field($model, 'factor_rh') ?>

    <?php // echo $form->field($model, 'lugar_nacimiento') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
