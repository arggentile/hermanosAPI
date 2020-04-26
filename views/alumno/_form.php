<?php

use app\models\Establecimiento;

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\widgets\ActiveField;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\web\View;
use kartik\widgets\DatePicker;

use app\assets\AlumnoAssets;
AlumnoAssets::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Alumno */
/* @var $modelPersona common\models\Persona */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
yii\bootstrap\Modal::begin([        
    'id'=>'modalfamilia',
    'class' =>'modal-scrollbar',
    'size'=>'modal-lg',
    ]);
    echo "<div id='modalContent'></div>";
yii\bootstrap\Modal::end();
?>

<div class="alumno-form">

    <?php $form = ActiveForm::begin(
        [
        'id'=>'form-empadronamiento',
        'enableClientValidation'=>true,
        'options' => [
            'class' => 'form-prev-submit'
         ],
        ]); ?>
    <?php
    echo Html::errorSummary([$modelPersona, $model], ['class'=>'alert alert-danger alert-dismissible','encode'=>true,'header'=>"
    
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <h4><i class='icon fa fa-ban'></i> Alert!</h4>
                Por favor corrija los errores del Formulario
              ", "footer"=>""]);
      ?>
    
    <div class="row form-group datagrupofamiliar">
        <div class="col-sm-2">
            <?php
            if(!$bloquearFamilia)
                echo Html::button('<i class="fa fa-search"></i> Buscar Familia', 
                        ['value'=> Url::to(['buscarFamilia']), 
                         'class'=>'btn btn-search', 'id'=>'buscarFamiliaBtn']);
            ?>
        </div>
        
        <div class="col-sm-3">        
            <div class="input-group">
                <span class="input-group-addon">Familia</span>
                <?= Html::activeInput('text',$modelGrupoFamiliar, 'apellidos', ['id' => 'apellidoFamilia', 'class' => 'form-control', 'readonly'=>true]); ?>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group">
                <span class="input-group-addon">Folio</span>
                <?= Html::activeInput('text', $modelGrupoFamiliar, 'folio', ['id' => 'folioFamilia', 'class' => 'form-control', 'readonly'=>true]); ?>
            </div>
        </div>
        
        <?php 
        if(!empty($modelGrupoFamiliar->id)) 
            echo   Html::hiddenInput('mifamilia',$modelGrupoFamiliar->id,['id'=>'mifamilia','name'=>'mifamilia']); 
        else
            echo   Html::hiddenInput('mifamilia','0',['id'=>'mifamilia','name'=>'mifamilia']); 
        ?> 

    </div>
    
          
    <?= app\widgets\formulariopersona\FormularioPersona::widget(['model' => $modelPersona]); ?>
   
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'nro_legajo') ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'xfecha_ingreso')->widget(
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
    
    
    
    <div class="row"  id="establecimientodivision">
        
        
        <div class="col-sm-3">
            <?= $form->field($model, 'establecimiento')->dropDownList($data['filtros']['dropEstablecimientosSearch'] ,
                    ['class' => '',
                    'prompt'=>'Seleccione',
                    'onchange'=>'
                        $.get( "'.Url::toRoute('establecimiento/drop-mis-divisionesescolares').'", { idEst: $(this).val() } )
                                    .done(function( data )
                                    {                                        
                                        $("#alumno-id_divisionescolar").html(data);
                                        $(\'#alumno-id_divisionescolar\').empty();                                        
                                        for(let i = 0; i < data.length; i++) {
                    
                                            let option = "<option value=\'"+ data[i].id +"\'>"+ data[i].nombre +"</option>";
                                            $(\'#alumno-id_divisionescolar\').append(option);
                                        }
                                    });'
                    ]);
            ?>
        </div>
       
        <div class="col-sm-3">
            <?= $form->field($model, 'id_divisionescolar')->dropDownList($data['filtros']['dropDivisionesSearch'],['prompt'=>'Seleccione...']) ?>
        </div>
    </div>
   
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'hijo_profesor')->dropDownList([0=>'No',1=>'Si'],['prompt'=>'Seleccione','options'=>[$model->xhijo_profesor=>['Selected'=>true]]]) ?>
        </div>
    </div>
    <?php
    if(!$model->isNewRecord){ ?>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'egresado')->dropDownList([0=>'No',1=>'Si'],['prompt'=>'Seleccione','options'=>[$model->egresado=>['Selected'=>true]]]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'xfecha_egreso')->widget(
                    DatePicker::className(),([
                                        'language'=>'es',
                                        'type' => DatePicker::TYPE_INPUT,
                                        'pluginOptions' => [
                                            'autoclose'=>true,
                                            'format' => 'dd-mm-yyyy'
                                        ]
                                    ])
                    )->label('Fecha Egreso');?>
        </div>
    </div>
     <?php } ?>
    <div class="form-group">
        <?= Html::submitButton("<i class='fa fa-save'></i> Guardar", ['class' => 'btn btn-save btn-flat btn-block','id'=>'btn-envio']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

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
                intro: 'Formulario Alta / Edición Alumno. <br /> Seleccione el grupo familiar, y complete los campos requeridos. '
            },  
            {
                element: document.querySelector('.datagrupofamiliar'),
                intro: 'Seleccione grupo familiar.'
            }, 
            {
                element: document.querySelector('#persona-apellido'),
                intro: 'Complete los datos personales del alumno.'
            },
                        
            
            {
                element: document.querySelector('#alumno-nro_legajo'),
                intro: 'Ingrese el Nro de legajo.'
            },
            {
                element: document.querySelector('#establecimientodivision'),
                intro: 'Seleccione el establecimiento y la división.'
            },
            {
                element: document.querySelector('#alumno-hijo_profesor'),
                intro: 'Indique si el mismo es hijo de profesor o no.'
            },
            
            
        ]
      });
      intro.start();
}
  
", \yii\web\View::POS_END,'ayuda');
?>