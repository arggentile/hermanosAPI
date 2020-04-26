<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\PersonaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Personas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="persona-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Persona', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'apellido',
            'nombre',
            'fecha_nacimiento',
            'id_sexo',
            //'id_tipodocumento',
            //'nro_documento',
            //'calle',
            //'nro_calle',
            //'piso',
            //'dpto',
            //'localidad',
            //'telefono',
            //'celular',
            //'mail',
            //'grupo_sanguineo',
            //'factor_rh',
            //'lugar_nacimiento',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
