<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\Url;

?>

<?php 
$form = ActiveForm::begin(
        [
        'id'=>'form-empadronamiento',
        'enableClientValidation'=>true,
        'options' => ['enctype' => 'multipart/form-data']
        ]); ?>
   
    
    

        <div class="col-sm-3">
            <?= $form->field($model, 'archivoentrante')->fileInput() ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton("<i class='fa fa-save'></i> Guardar...", ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','id'=>'btn-envio']) ?>
        </div>

    <?php ActiveForm::end(); ?>

     