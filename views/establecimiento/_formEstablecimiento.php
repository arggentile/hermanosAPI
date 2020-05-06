<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Establecimiento */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="establecimiento-form">

    <?php $form = ActiveForm::begin([
        'id'=>'form-establecimiento',
        'type'=> kartik\form\ActiveForm::TYPE_HORIZONTAL,
        'formConfig'=> ['labelSpan'=>2,]
    ]); ?>
    
            <?= $form->field($model, 'nombre') ?>
            <?= $form->field($model, 'iniciales') ?>
            <?= $form->field($model, 'xfecha_apertura')->widget(
                    DatePicker::className(),([
                                        'language'=>'es',                                        
                                        'pluginOptions' => [
                                            'autoclose'=>true,
                                            'format' => 'dd-mm-yyyy'
                                        ]
                                    ])
                    );?>
    
            <?= $form->field($model, 'calle') ?>
    
            <?= $form->field($model, 'telefono',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-phone"></i>']]]) ?>
    
            <?= $form->field($model, 'celular',['addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-phone"></i>']]]) ?>
    
            <?= $form->field($model, 'mail',['addon' => ['prepend' => ['content'=>'@']]])->input('email') ?>    
    
            <?= $form->field($model, 'nivel_educativo'); ?>
        

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <?= Html::submitButton("<i class='fa fa-save'></i> " . ' GUARDAR', ['class' => 'btn btn-save btn-flat btn-block', 'id' => 'btn-envio']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('  
$(document).ready(function(){
    $("#form-establecimiento").on("beforeValidate",function(e){
        $("#btn-envio").attr("disabled","disabled");
        $("#btn-envio").html("<i class=\'fa fa-spinner fa-spin\'></i> Procesando...");        
    });
    
    $("#form-establecimiento").on("afterValidate",function(e, messages){
        if ( $("#form-establecimiento").find(".has-error").length > 0){
            $("#btn-envio").removeAttr("disabled");
            $("#btn-envio").html("<i class=\'fa fa-save\'></i> Guardar...");
        }
    });
    
});         
', \yii\web\View::POS_READY,'js-establecimiento');
?>
<?php
$this->registerJs("      
function ayuda(){         
    var intro = introJs();
      intro.setOptions({
        nextLabel: 'Siguiente',
        prevLabel: 'Anterior',
        skipLabel:'Terminar',
        doneLabel:'Cerrar',
        steps: [      
            { 
                intro: 'Formulario Alta Establecimiento.'
            },  
            {
                element: document.querySelector('#form-establecimiento'),
                intro: 'Complete los campos segun corresponda.'
            },
            {
                element: document.querySelector('#btn-envio'),
                intro: 'Presione para confirmar el alta/edición.'
            },
        ]
      });
      intro.start();
} 
", \yii\web\View::POS_END,'ayuda');
?>