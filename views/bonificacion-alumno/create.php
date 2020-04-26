<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BonificacionAlumno */

$this->title = 'Create Bonificacion Alumno';
$this->params['breadcrumbs'][] = ['label' => 'Bonificacion Alumnos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bonificacion-alumno-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
