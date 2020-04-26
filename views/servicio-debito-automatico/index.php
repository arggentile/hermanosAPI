<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ServicioDebitoAutomatico */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Servicio Debito Automaticos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicio-debito-automatico-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Servicio Debito Automatico', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_debitoautomatico',
            'id_servicio',
            'tiposervicio',
            'resultado_procesamiento',
            //'linea',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
