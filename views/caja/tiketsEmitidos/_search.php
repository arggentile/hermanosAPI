<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\search\GrupoFamiliarSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tikets-search">

    <?php $form = ActiveForm::begin([        
        'method' => 'get',
        'id'=>'form-reporte-tikets',
        
    ]); ?>

    
    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($searchModelTiket, 'id')->input('text', ['placeholder'=>'Nro'])->label('Nro')?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($searchModelTiket, 'id_tipopago')->dropDownList(\app\models\FormaPago::getFormasPago(), ['prompt'=>'TODOS']) ?>    
        </div>
        <div class="col-sm-3">
            <?= $form->field($searchModelTiket, 'informada')->dropDownList(['0'=>'No','1'=>'Si'], ['prompt'=>'TODOS']) ?>    
        </div>
    </div>   
    
    <div class="row">
        <div class="col-sm-3" style="padding-right:0px;">                    
            <div class="form-group field-tiketsearch-fechas">
                <label class="control-label">Fecha Pago</label>
                <div class="input-group date">
                    <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                    </div>
                    <input class="form-control pull-right datepicker" type="text" name="TiketSearch[fecha_inicio_desde]" placeholder="" value="<?=(isset($searchModelTiket->fecha_inicio_desde) && !empty($searchModelTiket->fecha_inicio_desde))?$searchModelTiket->fecha_inicio_desde:'' ?>" />
                </div>
            </div>
        </div>
        <div class="col-sm-3" style="padding-left:0px;">
            <div class="form-group field-tiketsearch-fechas">
                <label class="control-label"> &nbsp;</label>
                <div class="input-group date">
                    <div class="input-group-addon">
                        a
                    </div>
                    <input class="form-control pull-right datepicker" type="text" name="TiketSearch[fecha_inicio_hasta]" placeholder="" value="<?=(isset($searchModelTiket->fecha_inicio_desde) && !empty($searchModelTiket->fecha_inicio_hasta))?$searchModelTiket->fecha_inicio_hasta:'' ?>" />
                </div>
            </div>
        </div>
        
        <div class="col-sm-4">
            <?= $form->field($searchModelTiket, 'id_cliente')->input('text', ['placeholder'=>'Folio Cliente'])->label('Cliente')?>
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
<?php
$this->registerJs("
    $(document).ready(function () {
   
    
    $('.datepicker').datetimepicker({
        sideBySide: true,
        defaultDate: false,
        focusOnShow: false,
        format: 'DD-MM-YYYY',        
        useCurrent: false,
        locale: 'es',
    });
    
    
});
", \yii\web\View::POS_READY);
?>
