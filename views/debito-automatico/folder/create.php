<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DebitoAutomatico */

$this->title = 'Create Debito Automatico';
$this->params['breadcrumbs'][] = ['label' => 'Debito Automaticos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="debito-automatico-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
