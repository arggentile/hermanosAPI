<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CategoriaServicioOfrecido */

$this->title = 'Create Categoria Servicio Ofrecido';
$this->params['breadcrumbs'][] = ['label' => 'Categoria Servicio Ofrecidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="categoria-servicio-ofrecido-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
