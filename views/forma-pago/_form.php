<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\FormaPago */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="forma-pago-form">

    <?php $form = ActiveForm::begin([
                    'enableClientValidation'=>false,
                    'options' => ['class' => 'form-ajax-crud']
                ]); ?>
    <div class="row">
        <div class="col-sm-6">
        <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
        <?= $form->field($model, 'siglas')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => 'btn btn-success btn-enviar invisible']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
