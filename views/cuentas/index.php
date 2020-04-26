<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\Cuentas */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cuentas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cuentas-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Cuentas', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'nombre',
            'fecha_apertura',
            'saldo_inicial',
            'saldo_actual',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
