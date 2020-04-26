<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ServicioDivisionEscolar */

$this->title = 'Update Servicio Division Escolar: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Servicio Division Escolars', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="servicio-division-escolar-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
