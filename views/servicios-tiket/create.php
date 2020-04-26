<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ServiciosTiket */

$this->title = 'Create Servicios Tiket';
$this->params['breadcrumbs'][] = ['label' => 'Servicios Tikets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicios-tiket-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
