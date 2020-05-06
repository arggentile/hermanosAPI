<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CategoriaBonificacion */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="categoria-bonificacion-form">

    <?php $form = ActiveForm::begin([
         'options' => ['class' => 'form-ajax-crud']
    ]); ?>

    <?= $form->field($model, 'descripcion')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'valor')->textInput(['maxlength' => true])->label('Valor Porcentual') ?>
    
    <?php
    $cantHermanos = ['1' => "1 Hermano", "2" => "2 Hermanos", "3" => "3 Hermanos", "4" => "4 Hermanos +"];
    echo $form->field($model, 'cantidad_hermanos')->dropDownList($cantHermanos, ['class'=>'form-control']) ?>
    <?= $form->field($model, 'activa')->checkbox() ?>
    <?= $form->field($model, 'automatica')->checkbox() ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => 'btn btn-success btn-enviar invisible']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>