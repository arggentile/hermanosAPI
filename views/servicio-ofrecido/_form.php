<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\ServicioOfrecido */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="servicio-ofrecido-form">
    <?php $form = ActiveForm::begin([
            'options' => [
                'class' => 'form-prev-submit'
             ],
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
   
    
    <?= $form->field($model, 'id_categoriaservicio')->dropDownList(app\models\CategoriaServicioOfrecido::getTipoServicios() ,['prompt'=>'Seleccione..']) ?>

    <?= $form->field($model, 'nombre') ?>

    <?= $form->field($model, 'descripcion') ?>

    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'importe',[
                    'inputTemplate' => '<div class="input-group"><i class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></i>{input}</div>',
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-6', 'label' => 'col-sm-6',
                                    ]]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'importe_hijoprofesor',[
                    'inputTemplate' => '<div class="input-group"><i class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></i>{input}</div>',
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-6', 'label' => 'col-sm-6',
                                    ]]) ?>
        </div>
    </div>

    <div class="row rowperiodo">
        <div class="col-sm-4">
            <?= $form->field($model, 'xfecha_inicio',[
                    'inputTemplate' => '<div class="input-group"><i class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></i>{input}</div>',
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-6', 'label' => 'col-sm-6',
                                    ]])->widget(
                                DatePicker::className(),([
                                'language'=>'es',
                                'type' => DatePicker::TYPE_INPUT,
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'dd-mm-yyyy'
                                ]
                            ])
            );?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'xfecha_fin',[
                    'inputTemplate' => '<div class="input-group"><i class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></i>{input}</div>',
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-6', 'label' => 'col-sm-6',
                                    ]])->widget(
                                DatePicker::className(),([
                                'language'=>'es',
                                'type' => DatePicker::TYPE_INPUT,
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'dd-mm-yyyy'
                                ]
                            ])
            );?>
        </div>
    </div>
       
    
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'devengamiento_automatico',[
                                        'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-6', 'label' => 'col-sm-6',
                                    ]])->dropDownList([0=>'No',1=>'Si'] ,
                                            ['prompt'=>'Seleccione..',
                                             'options'=>[$model->xdevengamiento_automatico=>['Selected'=>true]]]) ?>
          
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'xfecha_vencimiento',[
                    'inputTemplate' => '<div class="input-group"><i class="input-group-addon"><i class="glyphicon glyphicon-usd"></i></i>{input}</div>',
                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-6', 'label' => 'col-sm-6',
                                    ]])->widget(
                                DatePicker::className(),([
                                'language'=>'es',
                                'type' => DatePicker::TYPE_INPUT,
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'dd-mm-yyyy'
                                ]
                            ])
            );?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'activo',[
                                        'horizontalCssClasses' => [
                                        'wrapper' => 'col-sm-6', 'label' => 'col-sm-6',
                                    ]])->dropDownList([0=>'No',1=>'Si'] ,
                                            ['prompt'=>'Seleccione..',
                                             'options'=>[$model->activo=>['Selected'=>true]]]) ?>
          
        </div>       
    </div>
        
    <div class="box-footer">
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-8 ">
                <?= Html::submitButton('<i class="fa fa-save"></i> Guardar', ['class' => 'btn btn-select btnwidth100 btn-submit-envio',]) ?>
            </div>
        </div>
    </div>
       

    <?php ActiveForm::end(); ?>

</div>

<style type="text/css">
   #form-servicioofrecido .form-group {
    margin-bottom: 6px;
}
</style>
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
                intro: 'Formulario para el Alta/Edición Servicio Ofrecido.'
            },  
            {
                element: document.querySelector('#servicioofrecido-id_categoriaservicio'),
                intro: 'Seleccione la categoria del servicio.'
            }, 
            {
                element: document.querySelector('#servicioofrecido-nombre'),
                intro: 'Nombre del servicio a dar de alta.'
            },
            {
                element: document.querySelector('#servicioofrecido-importe'),
                intro: 'Importe del servicio.'
            },  
            {
                element: document.querySelector('#servicioofrecido-importe_hijoprofesor'),
                intro: 'Importe para los hijos de profesores.'
            },   
            {
                element: document.querySelector('.rowperiodo'),
                intro: 'Especifique el periodo en el que inicia y finaliza el servicio.'
            },  
            {
                element: document.querySelector('#servicioofrecido-xfecha_vencimiento'),
                intro: 'Especifique la fecha de vencimiento.  La misma especifica a partir de cuando comienza a considerarse como deudor un cliente si no abona el mismo',
            },  
                      
            {
                element: document.querySelector('.btn-submit-envio'),
                intro: 'Para concretar el alta presione sobre este botón.'
            },
        ]
      });
      intro.start();
}
        
", \yii\web\View::POS_END,'ayuda');
?>
<?php 
    $this->registerJsFile('@web/js/servicio-ofrecido.js', ['depends'=>[app\assets\AppAsset::className()]]);
?>