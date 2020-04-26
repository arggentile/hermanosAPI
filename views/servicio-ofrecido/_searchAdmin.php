<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\search\GrupoFamiliarSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="servicio-ofrecido-search">

    <?php $form = ActiveForm::begin([        
        'method' => 'get',
        'id'=>'form-search-serviciosofrecidos',        
    ]); ?>
    
    <div class="row">
        
        <div class="col-sm-4">
            <?= 
                $form->field($model, 'id_categoriaservicio')->dropDownList( $filtros['tiposervicios'],
                            ['class' => 'form-control', 
                             'prompt'=>'TODOS']);
            ?>    
        </div>
        <div class="col-sm-4">
           <?= $form->field($model, 'nombre') ?>
        </div>     
        <div class="col-sm-2">
            <?= 
                $form->field($model, 'devengamiento_automatico')->dropDownList( $filtros['sino'],
                            ['class' => 'form-control', 
                             'prompt'=>'TODOS'])->label('Devenga');
            ?> 
        </div>
        <div class="col-sm-2">
            <?= 
                $form->field($model, 'activo')->dropDownList( $filtros['sino'],
                            ['class' => 'form-control', 
                             'prompt'=>'TODOS'])->label('Activo');
            ?> 
        </div>        
    </div>

    <div class="row ">
        <div class="col-sm-12 form-group">
            <p class="pull-right">
                <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> Buscar', ['class' => 'btn btn-primary']) ?>
                <?= Html::button('<i class="glyphicon glyphicon-download-alt"></i> Exportar', ['class' => 'btn btn-export-listado']) ?>
            </p>
        </div>      
    </div>

    <?php ActiveForm::end(); ?>
</div>