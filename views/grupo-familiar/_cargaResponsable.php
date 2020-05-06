<?php
use common\models\TipoDocumento;
use common\models\TipoSexo;

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\widgets\ActiveField;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Persona */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-carga-responsable"> 
   
<?php 
$form = ActiveForm::begin([
    'id'=>'form-persona',
    'options' => ['class' => 'form-carga-responsable']
]); ?>
   
          
        <input type="hidden" name="idFamilia" id="idFamilia" value="<?= $modelResponsable->id_grupofamiliar; ?>" /> 
   
        <div class="row form-group">
            <div class="col-sm-10">        
                <div class="input-group">
                    <span class="input-group-addon"> 
                    <?= Html::activeLabel($modelResponsable, 'id_tipo_responsable', ['label'=>'Motivo Vinculación', 'class' => 'control-label','aria-required'=>"true"]) ?> </span>
                    <?= Html::activeDropDownList($modelResponsable, 'id_tipo_responsable', \app\models\TipoResponsable::getTipoResponsables(), ['prompt'=>'Select..','class'=>'form-control']); ?>
                </div>
                <?= Html::error($modelResponsable, 'id_tipo_responsable',['class'=>'text-error text-red']); ?>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-sm-10">        
                <div class="input-group">
                    <span class="input-group-addon"> 
                    <?= Html::activeLabel($modelResponsable, 'cabecera', ['label'=>'Es Responsable', 'class' => 'control-label', 'aria-required'=>"true"]) ?> </span>
                    <?= Html::activeDropDownList($modelResponsable, 'cabecera', ['0'=>'No', '1'=>'Si'], ['prompt'=>'Select..', 'class'=>'form-control']); ?>
                </div>
                <?= Html::error($modelResponsable, 'cabecera',['class'=>'text-error text-red']); ?>
            </div>
        </div>
        
        <?= app\widgets\formulariopersona\FormularioPersona::widget(['model' => $model]); ?>

        <div class="form-group group-invisible">
            <?= Html::submitButton($modelResponsable->isNewRecord ? '<i class=\'fa fa-save\'></i> Guardar' : '<i class=\'fa fa-save\'></i> Actualizar', ['id' => 'btn-enviar', 'class'=>'btn btn-save btn-flat btn-block', 'data-loading'=>"Aguarde"]) ?>
        </div>
       
<?php ActiveForm::end(); ?>
</div>
