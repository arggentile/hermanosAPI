<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DivisionEscolar */

$this->title = 'Create Division Escolar';
$this->params['breadcrumbs'][] = ['label' => 'Division Escolars', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="division-escolar-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
