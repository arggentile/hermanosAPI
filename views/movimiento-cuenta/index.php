<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\MovimientoCuenta */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Movimiento Cuentas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movimiento-cuenta-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Movimiento Cuenta', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_cuenta',
            'id_tipo_movimiento',
            'detalle_movimiento',
            'importe',
            //'fecha_realizacion',
            //'comentario',
            //'id_tipopago',
            //'id_hijo',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
