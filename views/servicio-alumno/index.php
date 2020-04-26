<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ServicioAlumnoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Servicio Alumnos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicio-alumno-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Servicio Alumno', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_servicio',
            'id_alumno',
            'fecha_otorgamiento',
            'fecha_cancelamiento',
            //'importe_servicio',
            //'importe_descuento',
            //'importe_abonado',
            //'id_estado',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
