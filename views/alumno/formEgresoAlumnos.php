<?php

use yii\helpers\Html;

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Alumno */

$this->title = 'Egreso de Alumnos';
$this->params['breadcrumbs'][] = ['label' => 'Alumnos', 'url' => ['listado']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-default box-colegio">
    <div class="box-header with-border">
        <i class="fa fa-user-plus"></i> <h3 class="box-title"> Egreso de  Alumnos  - <?= (empty($dataProviderAlumnos))?"PASO 1":"PASO 2"; ?></h3>
        <?php
        if (!empty($dataProviderAlumnos)){ ?>
        <div class="pull-right">
            <?= Html::a("<i class='fa fa-'></i> Volver paso 1", Url::to(['/alumno/egresar-alumnos']), ['class' => 'btn btn-default']) ?>
        </div>
        <?php } ?>
    </div>
    
    <div class="box-body">
        
        <div class="callout callout-egreso">
            <h4> Atención: </h4>
            <p> La tarea de egreso de Alumnos se lleva a cabo en dos pasos. <br />
            <ol>
                <li>  Debemos buscar los alumnos que deseamos egresar o migrar de division/estalecimiento. </li>
                <li>  Seleccionamos la opción de egreso deseada. <br /> 
                    <b> Egreso: </b> Si los alumnos migran del establecimiento. <br />
                    <b> Migrar: </b> Si se deséa realizar un pase de division/establecimiento. Para estó, 
                    se debe seleccionar el Establecimiento y la Division Escolar a realizar el pase. </li>
                
            </ol>
              
                
            </p>        
        </div>
        <?php
        if(empty($dataProviderAlumnos)) {
            $form = ActiveForm::begin([
                'id'=>'formSearchEgresoAlumnos',
                'enableClientValidation'=>true,
                'method'=>'GET'
            ]);   
        ?>
       <input type="hidden" name="searchAlumnos" id="searchAlumnos" value="1">
        <div class="row">
            <div class="col-sm-3">
                <?= Html::label('Establecimiento'); ?>
                <?= Html::dropDownList('id_establecimiento_ageresar', null, $data['filtros']['dropEstablecimientosSearch'], 
                    ['class' => 'form-control',
                    'prompt'=>'Seleccione',
                        'required'=>'required',
                    'onchange'=>'
                        $.get( "'.Url::toRoute('/establecimiento/drop-mis-divisionesescolares').'", { idEst: $(this).val() } )
                                    .done(function( data )
                                    {                                        
                                        $("#id_division_ageresar").html(data);
                                        $(\'#id_division_ageresar\').empty();                                        
                                        for(let i = 0; i < data.length; i++) {

                                            let option = "<option value=\'"+ data[i].id +"\'>"+ data[i].nombre +"</option>";
                                            $(\'#id_division_ageresar\').append(option);
                                        }
                                    });'
                    ]);
                ?>
            </div>

            <div class="col-sm-3">
                <?= Html::label('Division'); ?>
                <?=  Html::dropDownList('id_division_ageresar', null, [], ['id'=>'id_division_ageresar', 'class'=>'form-control', 'required'=>'required',]);?>
            </div>
            <div class="form-group">
                <?= Html::submitButton("<i class='fa fa-search'></i> Buscar Alumnos", ['class' => 'btn btn-search btn-submit-envio']) ?>
            </div>
        </div>
        
        <?php ActiveForm::end(); 
        }
        ?>
        
        <?php
        if(!empty($dataProviderAlumnos)){
            $formEgreso = ActiveForm::begin([
                'id'=>'formEgresoAlumnos',
                'enableClientValidation'=>true,
                'method'=>'POST'
            ]); 
        ?>
        <input type="hidden" name="division-egreso-inicial" id="division-egreso-inicial" value="<?= $divisionInicial;?>">
        <input type="hidden" name="establecimiento-egreso-inicial" id="establecimiento-egreso-inicial" value="<?= $establecimientoInicial;?>">
        <input type="hidden" name="egresaralumnos" id="egresaralumnos" value="0">
        
            <div class="row">                
                <div class="col-sm-2">
                    <?= $formEgreso->field($modelAlumnoEgreso, 'es_egreso')->dropDownList(['1'=>'EGRESO', '0'=>'MIGRAR'] ,
                        ['class' => 'form-control',
                        'prompt'=>'Seleccione',
                        'required'=>'required',                        
                        ])->label('Es Egreso');
                    ?>
                </div>
                
                <div class="col-sm-3" id="establegreso">
                    <?= $formEgreso->field($modelAlumnoEgreso, 'id_establecimiento')->dropDownList($data['filtros']['dropEstablecimientosSearch'] ,
                        ['class' => 'form-control',
                        'prompt'=>'Seleccione',
                       
                        'onchange'=>'
                            $.get( "'.Url::toRoute('/establecimiento/drop-mis-divisionesescolares').'", { idEst: $(this).val() } )
                                        .done(function( data )
                                        {                                        
                                            $("#egresoalumnoform-id_divisionescolar").html(data);
                                            $(\'#egresoalumnoform-id_divisionescolar\').empty();                                        
                                            for(let i = 0; i < data.length; i++) {

                                                let option = "<option value=\'"+ data[i].id +"\'>"+ data[i].nombre +"</option>";
                                                $(\'#egresoalumnoform-id_divisionescolar\').append(option);
                                            }
                                        });'
                        ])->label('Establecimiento');
                    ?>
                </div>

                <div class="col-sm-3" id="divisionegreso">
                    <?= $formEgreso->field($modelAlumnoEgreso, 'id_divisionescolar')->dropDownList($data['filtros']['dropDivisionesSearch'],['prompt'=>'Seleccione...'])->label('Division') ?>
                </div>
                
                <div class="col-sm-2">
                    <?= $formEgreso->field($modelAlumnoEgreso, 'fecha_egreso')->widget(
                                kartik\widgets\DatePicker::className(),([
                                                'language'=>'es',
                                                'type' => kartik\widgets\DatePicker::TYPE_INPUT,
                                                'options'=>['required'=>'required'],
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'dd-mm-yyyy',
                                                    'required'=>'required',
                                                ]
                                            ]))->label('Fecha Egreso');
                            ?>
                    <?php                            Html::error($modelAlumnoEgreso, 'fecha_egereso'); ?>
                </div>
                
                <div class="form-group col-sm-2">
                    <?= Html::submitButton("<i class='fa fa-save'></i> Egresar", ['class' => 'btn btn-success btn-submit-envio']) ?>
                    
                </div>
            </div>
        
        <?php       
        echo GridView::widget([
            'id'=>'grid-egreso-alumnos',
            'dataProvider' => $dataProviderAlumnos,               
            'columns' => [  
                [
                    'class' => 'yii\grid\CheckboxColumn', 
                    'checkboxOptions' => function($model) {
                          return ['value' => $model->id];
                      },
                ],
                [
                    'label' => 'Documento',
                    'attribute'=>'documento',
                    'value' => function($model) {
                        return $model->miPersona->nro_documento;
                    },
                ],
                [
                    'label' => 'Apellido',
                    'attribute'=>'apellido',
                    'value' => function($model) {
                        return $model->miPersona->apellido;
                    },
                ],
                [
                    'label' => 'Nombre',
                    'attribute'=>'nombre',
                    'value' => function($model) {
                        return $model->miPersona->nombre;
                    },
                ],
                [
                    'label' => 'Familia',
                    'attribute'=>'folio',                         
                    'value' => function($model) {
                        return $model->miGrupofamiliar->folio;
                    },
                ],
                [
                    'label' => 'Familia',
                    'attribute'=>'familia',                         
                    'value' => function($model) {
                        return $model->miGrupofamiliar->folio . " / ".$model->miGrupofamiliar->apellidos;
                    },
                ],  
                [
                    'label' => 'Establecimiento',
                    'attribute'=>'establecimiento',    
                    'value' => function($model) {
                        return $model->divisionescolar->establecimiento->nombre;
                    },
                ],              
                [
                    'label' => 'Division',
                    'attribute'=>'id_divisionescolar',    
                    'value' => function($model) {
                        return $model->divisionescolar->nombre;
                    },
                ],
            ],
        ]); 
        
        ActiveForm::end();
        }
        ?>
    </div>
</div>

<?php 
    $this->registerJsFile('@web/js/egresoAlumnos.js', ['depends'=>[app\assets\AppAsset::className()]]);
?>
<style type="text/css">
    <?php
    if($modelAlumnoEgreso->es_egreso=='0'){
       echo  "#establegreso, #divisionegreso{display: none;}";
    }
    ?>
    .callout-egreso{
        background-color: rgba(152, 181, 126, 0.35);
    }
</style>