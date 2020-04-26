<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\GrupoFamiliarSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="grupo-familiar-search">

<?php $form = ActiveForm::begin([        
    'method' => 'get',
    'id'=>'form-search-grupofamiliar',

]); ?>


    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'apellidos') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'folio') ?>    
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'id_pago_asociado')->dropDownList(\app\models\FormaPago::getFormasPago(), ['prompt'=>'TODOS']) ?>   
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'familias_activas')->checkbox(); ?>   
        </div>
     </div>
    
    
    

    <div class="row ">
        <div class="col-sm-12">
            <p class="pull-right">
                <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> Buscar', ['class' => 'btn btn-search']) ?>
                <?php
                if(Yii::$app->user->can('exportarFamilia'))
                    echo Html::button('<i class="glyphicon glyphicon-download-alt"></i> Exportar', ['class' => 'btn btn-export btn-export-listado']);
                ?>
            </p>
        </div>
      
    </div>

    <?php ActiveForm::end(); ?>

</div>