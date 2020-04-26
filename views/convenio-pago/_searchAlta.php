<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;

?>

<div id="form-cobroservicio" class="row">
    <div class="col-sm-10 col-sm-offset-1">        
        <div class="box box-warning">
            <div class="box-body">

                <?php $form = ActiveForm::begin(['id'=>'form-conveniopago',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'enableClientValidation' => false,
                    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_LARGE]]); ?>

                <div class="row">
                    <div class="col-sm-4">
                        <?= $form->field($modelGrupoFamiliar, 'apellidos',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-user"></i>']]])->input('text',['placeholder'=>'Ingrese Apellido']) ->label(false)?>
                    </div>

                    <div class="col-sm-4">
                        <?= $form->field($modelGrupoFamiliar, 'folio',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-user"></i>']]])->input('text',['placeholder'=>'Ingrese Folio']) ->label(false) ?>
                    </div>


                        <?= Html::submitButton('<i class=\'fa fa-search\'></i> Buscar', 
                        ['class' => 'btn btn-primary', 'id'=>'btn-envio']) ?>

                </div> 
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>  