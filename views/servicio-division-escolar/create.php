<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ServicioDivisionEscolar */

$this->title = 'Create Servicio Division Escolar';
$this->params['breadcrumbs'][] = ['label' => 'Servicio Division Escolars', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicio-division-escolar-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
