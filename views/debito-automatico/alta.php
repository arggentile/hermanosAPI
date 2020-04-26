<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DebitoAutomatico */

$this->title = 'Generación Archivo Debitos Automáticos';

?>
<div class="box box-default box-colegio" id="debito-automatico-create">
    <div class="box-header with-border">
        <i class="fa  fa-user-plus"></i><h3 class="box-title"> Débitos Automáticos </h3>
    </div>
    <div class="box-body">    
        <?= $this->render('_form', [
            'model' => $model,
            'filter' => $filter
        ]) ?>        
    </div>
</div>
