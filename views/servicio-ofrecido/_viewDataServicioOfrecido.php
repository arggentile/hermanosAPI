<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
?>
      
<div class="dropdown pull-right" id="drop-menu-servicioOfrecido">
    <button class="btn btn-default dropdown-toggle" type="button" id="servicioOfrecido" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Opciones
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="servicioOfrecido">
        <li>
        <?php    
            if(Yii::$app->user->can('gestionarServicios')){
                 echo Html::a('<i class="fa fa-pencil"></i> Actualizar', ['update', 'id' => $model->id], ['class' => '']);
            } ?>
        </li>
        <li>
        <?php
            if(Yii::$app->user->can('gestionarServicios')){
                echo Html::a('<i class="fa fa-trash-o"></i> Eliminar', 
                    ['delete', 'id' => $model->id], 
                    [
                        'class' => '',
                        'data' => [
                            'confirm' => 'Está seguro que desea eliminar el servicio?',
                            'method' => 'post',
                        ]
                    ]);
                    }?>
        </li>              
        <li role="separator" class="divider"></li>
        <li>
            <?= Html::a('<i class="fa fa-users"></i> Devengamiento', ['view', 'id' => $model->id], ['class' => '']); ?>                    
        </li>
        <li>
            <?= Html::a('<i class="fa fa-pencil"></i> Divisiones Asociadas', ['divisiones-asociadas', 'id' => $model->id], ['class' => '']); ?>
        </li>
    </ul>
</div>

<div class="row" id="dataservicio">
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
        <table>
            <tr>
                <td width="25%">                      
                </td>
                <td width="60%">
                    <h3 class="text-light-blue text-bold"> <?php echo $model->nombre; ?> </h3>             
                    <span class="text-light-blue text-bold">  Descripción: </span> <?php echo $model->descripcion; ?> <br /> 
                    <span class="text-light-blue text-bold">  Categoria: </span> <?php echo $model->categoriaservicio->descripcion; ?> <br /> 
                    <span class="text-light-blue text-bold">  Importes:  </span> <?php echo $model->importe . "  -  (H.Profesores: ".$model->importe_hijoprofesor.")";?> <br />
                    <span class="text-light-blue text-bold">  Periodo: </span> <?php echo $model->detallePeriodo; ?> <br />
                    <span class="text-light-blue text-bold">  Vencimiento Pago: </span> <?php echo Yii::$app->formatter->asDate($model->fecha_vencimiento); ?> <br />
                    <?php
                    if($model->activo)

                        echo "<span class='text-bold '> ACTIVO </span>";
                    else
                        echo "<span class='text-bold text-danger'> INACTIVO </span>";
                    ?>
                    <br />
                    <span class="text-light-blue text-bold">  Divisiones Asociadas: </span> <?= $model->cantDivisionesAsociadas; ?> 
                                    
                    <input type="hidden" name="cantdivisionesasociadas" id="cantdivisionesasociadas" value="<?= $model->cantDivisionesAsociadas; ?>" />
                    <?php //  Html::a('<i class="fa fa-eye"></i>', ['divisiones-asociadas', 'id' => $model->id], ['class' => 'btn btn-primary']);
                    ?>
                </td>
            </tr>                  
        </table>          
    </div>
</div> <!-- fin row dettales del establecimiento -->