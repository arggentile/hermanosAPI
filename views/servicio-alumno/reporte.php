<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ServicioOfrecido */

$this->title = 'Reporte Servicios Otorgados';

?>
<div class="box box-default">
    <div class="box-header with-border">
        <i class="fa fa-user-plus"></i> <h3 class="box-title"> Servicios Otorgados </h3> 
    </div>
    <div class="box-body">
        <?= app\widgets\buscadorServiciosAlumno\BuscadorServiciosAlumno::widget(
                [
                    'searchModel' => $searchModel,
                    'dataProvider'=>$dataProvider,
                    'buscador'=>true,
                    
                ]);
        ?>
    </div>
</div> <!-- box-Servicios Alumno -->

