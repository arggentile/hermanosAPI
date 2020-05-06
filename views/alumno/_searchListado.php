<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\search\GrupoFamiliarSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="grupo-familiar-search">
    <?php $form = ActiveForm::begin([        
        'method' => 'get',
        'id'=>'form-search-alumnos',        
    ]); ?>
    
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($modelPersona, 'nro_documento')->input('text', ['placeholder'=>'Nº documento'])?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($modelPersona, 'apellido')->input('text', ['placeholder'=>'Apellido']) ?>    
        </div>
        <div class="col-sm-4">
            <?= $form->field($modelPersona, 'nombre')->input('text', ['placeholder'=>'Nombre']) ?>    
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'nro_legajo')->input('text', ['placeholder'=>'Nº legajo']) ?>     
        </div>
        
        <div class="col-sm-2">
            <?= 
                $form->field($model, 'egresado')->dropDownList($filtros['sino'],
                    ['placeholder'=>'','empty' => '','class' => 'form-control','prompt'=>'TODOS']);
            ?>    
        </div>
        <div class="col-sm-2">
            <?= 
                $form->field($model, 'hijo_profesor')->dropDownList($filtros['sino'],
                    ['class' => 'form-control', 'empty' => '','prompt'=>'TODOS']);
            ?>    
        </div>
        <div class="col-sm-2">
            <?= 
                $form->field($model, 'activo')->dropDownList($filtros['sino'],
                    ['class' => 'form-control', 'empty' => '','prompt'=>'TODOS']);
            ?>    
        </div>             
    </div>
    
    <div class="row">
        <div class="col-sm-4">
            <?= 
                $form->field($model, 'establecimiento')->dropDownList($filtros['dropEstablecimientosSearch'],
                    ['class' => 'form-control',
                    'prompt'=>'',
                    'empty' => '',    
                    'onchange'=>'
                        $.get( "'.Url::toRoute('/establecimiento/drop-mis-divisionesescolares').'", 
                                    { idEst: $(this).val() } )
                                    .done(function( data )
                                    {             
                                        
                                        $(\'#alumnosearch-id_divisionescolar\').empty();
                                        let option = "<option value=\'\'></option>";
                                        $(\'#alumnosearch-id_divisionescolar\').append(option);
                                        for(let i = 0; i < data.length; i++) {
                    
                                            let option = "<option value=\'"+ data[i].id +"\'>"+ data[i].nombre +"</option>";
                                            $(\'#alumnosearch-id_divisionescolar\').append(option);
                                        }
                                    });'
                    ]);
            ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'id_divisionescolar')->dropDownList($filtros['dropDivisionesSearch'],['prompt'=>'']) ?>    
        </div>        
    </div>
        

    <div class="row ">
        <div class="col-sm-12">
            <p class="pull-right">
            <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> Buscar', ['class' => 'btn btn-search','data-loading-text'=>"Aguarde..."]) ?>
            <?php
            if(Yii::$app->user->can('exportarAlumno'))
                echo Html::button('<i class="glyphicon glyphicon-download-alt"></i> Exportar', 
                        ['class' => 'btn btn btn-export btn-export-listado',
                            ]);
            ?>
            </p>
        </div>
      
    </div>

    <?php ActiveForm::end(); ?>

</div>