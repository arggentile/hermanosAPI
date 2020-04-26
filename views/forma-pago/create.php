<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\FormaPago */

$this->title = 'Alta Forma Pago';
$this->params['breadcrumbs'][] = ['label' => 'Forma Pagos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="forma-pago-create">    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
