<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ServicioAlumno */

$this->title = 'Create Servicio Alumno';
$this->params['breadcrumbs'][] = ['label' => 'Servicio Alumnos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicio-alumno-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
