<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TipoSexo */

$this->title = 'Create Tipo Sexo';
$this->params['breadcrumbs'][] = ['label' => 'Tipo Sexos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipo-sexo-create">  
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>