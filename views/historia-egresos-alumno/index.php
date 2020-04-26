<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\HistoriaEgresosAlumnoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Historia Egresos Alumnos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="historia-egresos-alumno-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Historia Egresos Alumno', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_alumno',
            'id_division_actual',
            'id_division_egreso',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
