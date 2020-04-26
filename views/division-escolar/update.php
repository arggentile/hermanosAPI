<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DivisionEscolar */

$this->title = 'Update Division Escolar: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Division Escolars', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="division-escolar-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
