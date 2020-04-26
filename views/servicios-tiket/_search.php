<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\ServiciosTiketSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="servicios-tiket-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_tiket') ?>

    <?= $form->field($model, 'id_servicio') ?>

    <?= $form->field($model, 'tiposervicio') ?>

    <?= $form->field($model, 'monto_abonado') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
