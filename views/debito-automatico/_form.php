<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\models\DebitoAutomatico */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="debito-automatico-form">

    <?php 
    $form = ActiveForm::begin([
        'id'=>'form-debitoautomatico'
        ]); ?>
    
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
        
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'tipo_archivo')->dropDownList($filter['tipoarchivo'] ,['prompt'=>'Seleccione..']); ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-3">
            <?php 
            $label = 'Fecha Vencimiento Debito ';
            $label.= Html::tag('helpFecha', $content = '<i class="glyphicon glyphicon-question-sign"></i>', [
                   // html-tags won't be rendered in title
                   'title'=>"Indicamos la fecha final del barrido o fecha en el que se realiza el debito en el Banco",
                   'data-placement' => 'left' ,
                   'data-toggle'=>'tooltip',
                   'style' => 'white-space:pre;'
            ] );            
            ?>
            <?= $form->field($model, 'xfecha_debito')->widget(
                    DatePicker::className(),([
                                        'language'=>'es',
                                        
                                        'pluginOptions' => [
                                            'autoclose'=>true,
                                            'format' => 'dd-mm-yyyy'
                                        ]
                                    ]))->label($label);?>
        </div>
    </div>
    
    <div class="row" id='periodo'>
        <div class="col-sm-3">
            <?=
            
            
            $form->field($model, 'xinicio_periodo')->widget(
                    DatePicker::className(),([
                                        'language'=>'es',
                                        
                                        'pluginOptions' => [
                                            'autoclose'=>true,
                                            'format' => 'dd-mm-yyyy'
                                        ]
                                    ]));?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'xfin_periodo')->widget(
                    DatePicker::className(),([
                                        'language'=>'es',
                                        'type' => DatePicker::TYPE_INPUT,
                                        'pluginOptions' => [
                                            'autoclose'=>true,
                                            'format' => 'dd-mm-yyyy'
                                        ]
                                    ]));?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-6  form-group">

            <?= Html::submitButton('<i class=\'fa fa-save\'></i> Generar',
                    ['class' => 'btn btn-select btnwidth100 btn-submit-envio', 'id' => 'btn-envio'])
            ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs("
$(document).ready(function(){
    $('#form-debitoautomatico').on('beforeValidate',function(e){
        $('#btn-envio').attr('disabled','disabled');
        $('#btn-envio').html('<i class=\'fa fa-spinner fa-spin\'></i> Procesando...');        
    });
    $('#form-debitoautomatico').on('afterValidate',function(e, messages){
        if ($('#form-debitoautomatico').find('.has-error').length > 0){
            $('#btn-envio').removeAttr('disabled');
            $('#btn-envio').html('<i class=\'fa fa-save\'></i> Guardar...');
        }
    });
    
});         
", \yii\web\View::POS_READY,'preventSubmitForm');
?>
