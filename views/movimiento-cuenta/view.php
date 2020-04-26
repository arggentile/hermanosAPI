<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MovimientoCuenta */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Movimiento Cuentas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="movimiento-cuenta-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'id_cuenta',
            'id_tipo_movimiento',
            'detalle_movimiento',
            'importe',
            'fecha_realizacion',
            'comentario',
            'id_tipopago',
            'id_hijo',
        ],
    ]) ?>

</div>
