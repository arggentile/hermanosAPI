<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DivisionEscolar */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="division-escolar-form">

    <?php $form = ActiveForm::begin([
                    'id'=>'form-divisiones',
                    'layout'=>'horizontal',                   
                    'enableClientValidation'=>true,
                    'options' => ['class' => 'form-ajax-crud']
                    ]); ?>
    
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
    
            <?= $form->field($model, 'iniciales')->textInput(['maxlength' => true]) ?>
    
            <?php
                $divisiones = \app\models\DivisionEscolar::find()->joinWith('establecimiento e')->where(['=', 'e.id', $modelEstablecimiento->id])->asArray()->all();
                $divisiones = yii\helpers\ArrayHelper::map($divisiones, 'id', 'nombre');
            ?>
            <?= $form->field($model, 'id_divisionegreso')->dropDownList($divisiones,['prompt'=>'']) ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => 'btn btn-success btn-enviar invisible']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
