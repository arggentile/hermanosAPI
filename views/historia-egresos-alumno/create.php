<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\HistoriaEgresosAlumno */

$this->title = 'Create Historia Egresos Alumno';
$this->params['breadcrumbs'][] = ['label' => 'Historia Egresos Alumnos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="historia-egresos-alumno-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
