<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ServiciosTiketSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Servicios Tikets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicios-tiket-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Servicios Tiket', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_tiket',
            'id_servicio',
            'tiposervicio',
            'monto_abonado',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
