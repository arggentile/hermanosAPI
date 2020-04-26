<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a('SiCaP', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
            
                
                <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-life-bouy"></i> Ayuda
                        
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header"> <a class="" href="javascript:void(0);" onclick="javascript:ayuda();">Nesecito Ayuda?</a>  </li>
                        <li class="header"> <a class="" href="<?= yii\helpers\Url::to(['/ayuda/videos-tutoriales']); ?>">Videos Tutoriales</a>  </li>
                       
                        
                    </ul>
                </li>
                <li>
                    <a href="<?= yii\helpers\Url::to (['/user/security/logout']); ?>" data-method="post"><i class="fa fa-circle-o"> Salir</i></a>
                </li>
                <!-- User Account: style can be found in dropdown.less -->
                
            </ul>
        </div>
    </nav>
</header>