<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

use app\assets\GrupoFamiliarAsset;
GrupoFamiliarAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\GrupoFamiliarSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Grupo Familiar';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-default box-colegio grupo-familiar-index">
    <div class="box-header with-border">
        <i class="fa fa-users"></i> <h3 class="box-title"> Listado Familias </h3>    
        <span class="pull-right">
            <?php
              if (Yii::$app->user->can('cargarFamilia'))
                  echo Html::a('<i class="fa fa-plus-square"></i> Alta', ['alta'], ['class' => 'btn btn-primary btn-xs btn-alta-listado','id'=>'btn-alta']);
            ?>
        </span>
    </div>
    <div class="box-body">
       
        <?php
        echo $this->render('_searchListado', ['model' => $searchModel]);
        ?>
        
        <?php Pjax::begin([
            'id'=>'pjax-familias',
            'enablePushState' => false,  
            'timeout'=>false
            ]); 
        ?>  
            
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,                   
            'columns' => [                        
                'apellidos',                       
                'folio',                       
                [
                    'label' => "Hijos: <i class='fa fa-user'></i>Activos  <span class='text-red'><i class='fa fa-user text-red'></i>Inactivos</span>",  
                    'format'=>'raw',
                    'headerOptions' => ['class'=>'hijosalumnos'],
                    'encodeLabel' => false,
                    'value' => function($model) {
                        $detalleHijos='';
                        $i=0;
                        if(!empty($model->alumnos))
                            foreach($model->alumnos as $hijo){
                                $i+=1;
                                if($hijo->activo)
                                    $detalleHijos.= "<small><i class='fa fa-user'></i> ".$hijo->miPersona->apellido .", ".$hijo->miPersona->nombre."</small><br />";
                                else
                                    $detalleHijos.= "<span class='text-red'><small><i class='fa fa-user'></i> ".$hijo->miPersona->apellido .", ".$hijo->miPersona->nombre."</small></span><br />";

                                }
                        return $detalleHijos;
                    },
                ],
                [
                    'label' => 'Pago Aderido',
                    'attribute'=>'id_pago_asociado',
                    'headerOptions' => ['class'=>'pagoasociado'],
                    'filter'=> dmstr\helpers\Html::activeDropDownList($searchModel, 'id_pago_asociado', app\models\FormaPago::getFormasPago(),['prompt'=>'','class'=>'form-control']),
                    'value' => function($model) {
                        return $model->pagoAsociado->nombre;
                    },
                ],
                ['class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['width' => '50','class'=>'actionsgrid'],
                'visibleButtons' => [                                   
                    'update' => Yii::$app->user->can('cargarFamilia'),
                    'view' => Yii::$app->user->can('visualizarFamilia'),
                    ],
                'template' => '{view}{update}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            $url = yii\helpers\Url::to(['grupo-familiar/actualizar', 'id' => $model->id]);
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                        },
                    ],
                ],
            ],
        ]);
        ?>   
        <?php Pjax::end(); ?>
            
        
    </div>
</div>




<?php
$this->registerJs("
    $('#form-search-grupofamiliar').on('beforeSubmit', function (e) {     
        e.preventDefault();
        
        dataOptionPjax = 
        {
            url: '" . Url::current() ."',
            container: '#pjax-familias', 
            timeout: false,
            data: $('#form-search-grupofamiliar').serialize()
        };
            
        $.pjax.reload(dataOptionPjax);  
        return false;
    });    

    $('#form-search-grupofamiliar .btn-export-listado').click(function(){
        var curr_page = window.location.href;
        if (curr_page.indexOf('?') !== -1)
            var curr_page =  curr_page +  '&export=1';
        else
            var curr_page = curr_page +  '?export=1';
        window.open(curr_page,'_blank');       
    });
    
", \yii\web\View::POS_READY);
?>

<script type="text/javascript">
function ayuda(){         
    var intro = introJs();
      intro.setOptions({
        nextLabel: 'Siguiente',
        prevLabel: 'Anterior',
        skipLabel:'Terminar',
        doneLabel:'Cerrar',
        steps: [      
            { 
                intro: "Listado de Familias."
            },  
            {
                element: document.querySelector('.grupo-familiar-search'),
                intro: "Filtros para realizar busquedas específicas."
            },            
            {
                element: document.querySelector('.grid-view tbody'),
                intro: "El resultado de la busqueda sera desplegado en esta sección."
            },           
            {
                element: document.querySelector('.hijosalumnos'),
                intro: "Hijos Alumnos, que tuvieron contacto con algun Establecimiento."
            },            
            {
                element: document.querySelector('.pagoasociado'),
                intro: "Medio pago adoptado."
            },
            {
                element: document.querySelector('.actionsgrid'),
                intro: "Acciones para visualizar o editar alumno."
            },
            {
                element: document.querySelector('#btn-alta'),
                intro: "Alta de Grupo Familiar."
            },
            {
                element: document.querySelector('.btn-export'),
                intro: "Descargar listado en excel."
            },
        ]
      });
      intro.start();
}      
</script>