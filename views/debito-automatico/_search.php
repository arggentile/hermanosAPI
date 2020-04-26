<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\DebitoAutomaticoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="debito-automatico-search">

    <?php $form = ActiveForm::begin([
        'id'=>'form-search-debitosautomaticos',        
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'nombre') ?>
        </div>
        <div class="col-sm-4">
            <?= 
                $form->field($model, 'tipo_archivo')->dropDownList($filter['tipoarchivo'],
                    ['class' => 'form-control', 'prompt'=>'TODOS']);
            ?>       
        </div>
        <div class="col-sm-2">
            <?= 
                $form->field($model, 'procesado')->dropDownList(['0'=>'NO', '1'=>'SI'],
                    ['class' => 'form-control', 'prompt'=>'TODOS']);
            ?>    
        </div>
    </div>
    
    
    <div class="row ">
        <div class="col-sm-12">
            <p class="pull-right">
            <?= Html::submitButton('<i class="glyphicon glyphicon-search""></i> Buscar', ['class' => 'btn btn-primary']) ?>        
            <?= Html::button('<i class="glyphicon glyphicon-download-alt"></i> Exportar', ['class' => 'btn btn btn-export-listado btn-export-listado']); ?>
           
            </p>
        </div>
    </div>
 

    <?php ActiveForm::end(); ?>

</div>
