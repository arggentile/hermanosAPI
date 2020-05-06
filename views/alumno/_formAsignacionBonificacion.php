<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BonificacionFamiliar */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bonificacion-familiar-form">

    <?php $form = ActiveForm::begin([
        'id'=>'formAsignacionBonificacion',
        'enableClientValidation'=>true,
    ]); ?>
    <div class="row">
        <div class="col-sm-6">

        <?= $form->field($model, 'id_bonificacion')->dropDownList(app\models\Bonificaciones::getDetalleBonificacionesActivasAlumnoDrop(),
                    ['class' => 'form-control',
                    'prompt'=>'Seleccione Bonificación'])->label(false); ?>
        </div>
        <?= Html::submitButton('<i class="fa fa-share-square-o"></i> ASIGNAR', ['class' =>'btn btn-success']) ?>    
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs("      
    $('#formAsignacionBonificacion').on('beforeValidate',function(e){
        $('#formAsignacionBonificacion button').attr('disabled','disabled');
        $('#formAsignacionBonificacion button').html('<i class=\'fa fa-spinner fa-spin\'></i> Procesando...');        
    });
    
    $('#formAsignacionBonificacion').on('afterValidate',function(e, messages){
        if ( $('#formAsignacionBonificacion').find('.has-error').length > 0){
            $('#formAsignacionBonificacion button').removeAttr('disabled');
            $('#formAsignacionBonificacion button').html('<i class=\'fa fa-save\'></i> Asignar...');
        }
    });


", \yii\web\View::POS_END,'ayuda');
?>

    