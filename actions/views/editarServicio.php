<?php

use yii\helpers\Html;
?>
<div class="servicio-alumno-form">
   <?php 
    $form = yii\bootstrap\ActiveForm::begin([
        'id'=>'form-edit-servicioalumno',
        'layout'=> 'horizontal',
        'fieldConfig' => [       
        'horizontalCssClasses' => [
            'label' => 'col-sm-2',
            'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-6',
            'error' => '',
            'hint' => '',
        ],
    ]
    ]); ?>
    <?php
    echo Html::errorSummary([$model], ['class'=>'alert alert-danger alert-dismissible','encode'=>true,'header'=>"    
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <h4><i class='icon fa fa-ban'></i> Alert!</h4>
                Por favor corrija los errores del Formulario
              ", "footer"=>""]);
      ?>
    
     <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'importe_servicio',[
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-4', 'label' => 'col-sm-3',
                                    ]]) ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'importe_descuento',[
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-4', 'label' => 'col-sm-3',
                                    ]]) ?>
        </div>
    </div>
    
     
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'importe_abonado',[
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-4', 'label' => 'col-sm-3',
                                    ]]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
                <?= $form->field($model, 'id_estado',[
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-6', 'label' => 'col-sm-3',
                                    ]])->dropDownList( [\app\models\EstadoServicio::ID_ABIERTA =>'Adeuda', \app\models\EstadoServicio::ID_ABONADA => 'Abonada'],
                        ['prompt'=>'Seleccione...'])->label('Estado') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?= Html::submitButton("<i class='fa fa-save'></i> Guardar", ['class' => 'btn btn-primary btn-flat btn-block','id'=>'btn-envio']) ?>
        </div>
    </div>
    <?php yii\bootstrap\ActiveForm::end(); ?>
</div>