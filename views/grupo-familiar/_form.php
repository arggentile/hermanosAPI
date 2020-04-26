<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use app\assets\GrupoFamiliarAsset;
GrupoFamiliarAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\GrupoFamiliar */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="grupo-familiar-form">
    <?php $form = ActiveForm::begin([
        'id'=>'form-grupofamiliar',
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
   
            <?= $form->field($model, 'apellidos') ?>
            <?= $form->field($model, 'descripcion') ?>
      

    <div class="row">
        <div class="col-sm-5">
            <?= $form->field($model, 'folio',[
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-7', 'label' => 'col-sm-5',
                                    ]]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-5">
            <?= $form->field($model, 'id_pago_asociado',[
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-7', 'label' => 'col-sm-5',
                                    ]])->dropDownList(\app\models\FormaPago::getFormasPago(),['prompt'=>'Select...']) ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-8">
                    <?php 
            if($model->id_pago_asociado==4)
                echo $form->field($model, 'cbu_cuenta',[
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-7', 'label' => 'col-sm-3',
                                    ]])->textInput();
            else
                echo $form->field($model, 'cbu_cuenta',[
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-7', 'label' => 'col-sm-3',
                                    ]])->textInput(['readonly' => true]);?>

          
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
              <?php 
            if($model->id_pago_asociado==5)
                echo $form->field($model, 'nro_tarjetacredito',[
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-6', 'label' => 'col-sm-6',
                                    ]])->textInput()->label('Nº TC');
            else
                echo $form->field($model, 'nro_tarjetacredito',[
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-6', 'label' => 'col-sm-6',
                                    ]])->textInput(['readonly' => true])->label('Nº TC');?>
           
        </div>
        <div class="col-sm-4">
              <?php 
            if($model->id_pago_asociado==5)
                echo $form->field($model, 'prestador_tarjeta',[
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-6', 'label' => 'col-sm-6',
                                    ]])->textInput()->label('Prestador');
            else
                echo $form->field($model, 'prestador_tarjeta',[
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-6', 'label' => 'col-sm-6',
                                    ]])->textInput(['readonly' => true])->label('Prestador');?>
           
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8">
              <?php 
            if($model->id_pago_asociado==5 || $model->id_pago_asociado==4 )
                echo $form->field($model, 'tarjeta_banco')->textInput()->label('Banco');
            else
                echo $form->field($model, 'tarjeta_banco',
                                    ['horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-7', 'label' => 'col-sm-3',
                                    ]])->textInput(['readonly' => true])->label('Banco'); 
                
                ?>
           
        </div>        
    </div>
    
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-8">
                <?= Html::submitButton("<i class='fa fa-save'></i> Guardar", ['class' => 'btn btn-save btn-flat btn-block ','id'=>'btn-envio']) ?>
            </div>
        </div>
    
    

    <?php ActiveForm::end(); ?>
</div>


<script type="text/javascript">
function ayuda(){         
    var intro = introJs();
      intro.setOptions({
        nextLabel: 'Siguiente',
        prevLabel: 'Anterior',
        skipLabel:'Terminar',
        doneLabel:'Cerrar',
        steps: [      
            { 
                intro: "Formulario Alta de Grupo Familiar."
            },  
                       
            {
                element: document.querySelector('#grupofamiliar-apellidos'),
                intro: "Apellido del grupo familiar."
            },
            {
                element: document.querySelector('#grupofamiliar-folio'),
                intro: "Nro de folio."
            },
            {
                element: document.querySelector('#grupofamiliar-id_pago_asociado'),
                intro: "Indique el pago asosiado, segun el mismo debe completar el CBU o NRO de TC."
            },           
            {
                element: document.querySelector('#btn-envio'),
                intro: "Presione para confirmar el alta."
            },
        ]
      });
      intro.start();
}      
</script>