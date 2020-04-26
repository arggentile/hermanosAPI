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

    <?= $form->field($model, 'valor')->textInput(['maxlength' => true]) ?>

    

    <?= $form->field($model, 'activa')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => 'btn btn-success btn-enviar invisible']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
