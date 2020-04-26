<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FormaPago */

$this->title = 'Update Forma Pago: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Forma Pagos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="forma-pago-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
