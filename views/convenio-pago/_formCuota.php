<?php
use dmstr\helpers\Html;
?>
<div class="row form-group cuota-convenio groupcuota" id="divcuota-<?= $ordn; ?>">
    <?=    \yii\helpers\Html::activeHiddenInput($model, "[$ordn]id"); ?>
    <div class="col-sm-5">        
        <div class="input-group">
            <span class="input-group-addon"> Fecha </span>
                <?php
                        
                        echo \kartik\date\DatePicker::widget([
                            'model' => $model,
                            'type' => \kartik\widgets\DatePicker::TYPE_INPUT,
                            
                            'attribute' => "[$ordn]xfecha_establecida",      
                            'pickerIcon'=> '<i class="glyphicon glyphicon-calendar kv-dp-icon"></i>',
                            'pluginOptions' => [
                                'autoclose'=>false,
                                'format' => 'dd-mm-yyyy'
                            ],
                            'language' => 'es',
                            'readonly' => ($model->id_estado !== \app\models\EstadoServicio::ID_ABIERTA)?true:false, 
                            'disabled' => ($model->id_estado !== \app\models\EstadoServicio::ID_ABIERTA)?true:false,
                        ]);
                    
                ?>
        </div>
        <?= Html::error($model, "[$ordn]fecha_establecida",['class'=>'text-error text-red']); ?>
    </div>
    <div class="col-sm-3">        
        <div class="input-group">
            <span class="input-group-addon"> $ </span>
            
            <?php
            if($model->id_estado !== \app\models\EstadoServicio::ID_ABIERTA)
                echo Html::activeInput('text', $model, "[$ordn]monto",[ 'class'=>'form-control', 'readonly'=>'readonly'   ]); 
            else
               echo Html::activeInput('text', $model, "[$ordn]monto",[ 'class'=>'form-control',    ]);  ?>
        </div>
        <?= Html::error($model, "[$ordn]monto",['class'=>'text-error text-red']); ?>
    </div>
    <div class="col-sm-1">
         <?php
            if($model->id_estado == \app\models\EstadoServicio::ID_ABIERTA){ ?>
                
                <button type="button" class="btn btn-xs btn-danger" onclick="eliminarcuota(<?= $ordn; ?>);" ><i class="fa fa-remove"></i></button>
            <?php } ?>
    </div>     
</div>

