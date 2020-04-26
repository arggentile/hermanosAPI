<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Cuentas */

$this->title = 'Create Cuentas';
$this->params['breadcrumbs'][] = ['label' => 'Cuentas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cuentas-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
