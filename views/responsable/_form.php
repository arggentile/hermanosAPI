<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Responsable */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="responsable-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_grupofamiliar')->textInput() ?>

    <?= $form->field($model, 'id_persona')->textInput() ?>

    <?= $form->field($model, 'id_tipo_responsable')->textInput() ?>

    <?= $form->field($model, 'cabecera')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
