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
        'id'=>'form-search-conveniospago',
        
    ]); ?>

    
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'mifamilia')->label('Familia')?>
        </div>
        <div class="col-sm-4">
            <?= 
                $form->field($model, 'con_servicios')->dropDownList(
                    ['0'=>'NO','1'=>'SI'],
                    ['class' => 'form-control', 'prompt'=>'TODOS']);
            ?>   
        </div>
        <div class="col-sm-4">
            <?= 
                $form->field($model, 'adeudan')->dropDownList(
                    ['0'=>'NO','1'=>'SI'],
                    ['class' => 'form-control', 'prompt'=>'TODOS']);
            ?>   
        </div>
    </div>

    <div class="row ">
        <div class="col-sm-12 form-group">
            <p class="pull-right">
            <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> Buscar', ['class' => 'btn btn-search']) ?>
            <?php
            if(Yii::$app->user->can('exportarAlumno'))
                echo Html::button('<i class="glyphicon glyphicon-download-alt"></i> Exportar', ['class' => 'btn btn-export btn-export-listado btn-export-listado']);
            ?>
            </p>
        </div>      
    </div>

    <?php ActiveForm::end(); ?>

</div>