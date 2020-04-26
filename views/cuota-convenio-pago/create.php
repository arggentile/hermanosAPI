<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CuotaConvenioPago */

$this->title = 'Create Cuota Convenio Pago';
$this->params['breadcrumbs'][] = ['label' => 'Cuota Convenio Pagos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cuota-convenio-pago-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
