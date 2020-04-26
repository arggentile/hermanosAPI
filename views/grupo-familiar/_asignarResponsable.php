<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

?>
<div class="">
    <?php $form = ActiveForm::begin([        
        'method' => 'get',
        'id'=>'form-asignacion-responsbale-grupofamiliar',
    ]); ?>
    
    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($searchModel, 'nro_documento') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($searchModel, 'apellido') ?>    
        </div>
        <div class="col-sm-4">
            <?= $form->field($searchModel, 'nombre') ?>    
        </div>      
        <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i>  Buscar', ['class' => 'btn btn-search btn-busqueda']) ?> 
    </div>     

    <?php ActiveForm::end(); ?>
</div>

<?php
\yii\widgets\Pjax::begin(
 [
    'id'=>'pjax-responsables-grupofamiliar',
    'enablePushState' => false,
    'enableReplaceState' => false,
    'timeout'=>false,
]); 
?>

<div class="table-responsive">
    <?php echo GridView::widget([
        'id'=>'buscadorPersonaWidget',
        'dataProvider' => $dataProvider, 
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
        'headerRowOptions' => ['class'=>'x'],
        'columns' => [                
                
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{select}',
                    'headerOptions' => ['class'=>'cuadroselecion'],
                    'header'=>\dmstr\helpers\Html::dropDownList('tipores','',app\models\TipoResponsable::getTipoResponsables(),['class'=>'','id'=>'tipores']) ,
                    'buttons' => [                                    
                        'select' => function ($url, $model, $key) { 
                            return Html::button('Asignar', 
                                ['value'=> Url::to(['grupo-familiar/asignar-responsable', 'idresponsable'=>$model['id']]),
                                 'class' => 'btn btn-select btn-xs bt-asign-responsablefamilia',
                                 'onclick'=>'js:{asignarResponsable(this);}'    
                                ]);

                        }
                    ],                            
                ],    
                
                'nro_documento',                
                'apellido',
                'nombre',
        ],
    ]); ?>
</div>

</div>
<?php \yii\widgets\Pjax::end() ?>

<?php
$this->registerJs("
    $('#form-asignacion-responsbale-grupofamiliar').on('beforeSubmit', function (e) { 
        e.preventDefault();
    
        $('#form-asignacion-responsbale-grupofamiliar  .btn-busqueda').attr('disabled','disabled');
        $('#form-asignacion-responsbale-grupofamiliar  .btn-busqueda').html('<i class=\'fa fa-spinner fa-spin\'></i> Procesando..');    
        
        dataOptionPjax = 
        {
            url: '" . Url::current() ."',
            container: '#pjax-responsables-grupofamiliar', 
            push: false,
            replace: false,
            timeout: false,
            data: $('#form-asignacion-responsbale-grupofamiliar').serialize()
        };
            
        $.pjax.reload(dataOptionPjax);  
        return false;
    }); 
    
    
    $(document).on('pjax:error', '#pjax-responsables-grupofamiliar', function(xhr, textStatus, error, options) { 
            reportarNotificacionGral('Error interno procesando la solicitud', 'error', true);    
            $('#form-asignacion-responsbale-grupofamiliar  .btn-busqueda').removeAttr('disabled','disabled');
            $('#form-asignacion-responsbale-grupofamiliar  .btn-busqueda').html('<i class=\'glyphicon glyphicon-search\'></i> Buscar');  
            return false;
    });
    
    $(document).on('pjax:complete', '#pjax-responsables-grupofamiliar', function(e) { 
        $('#form-asignacion-responsbale-grupofamiliar  .btn-busqueda').removeAttr('disabled','disabled');
        $('#form-asignacion-responsbale-grupofamiliar  .btn-busqueda').html('<i class=\'glyphicon glyphicon-search\'></i> Buscar');    
    });
", \yii\web\View::POS_READY);
?>

<script type="text/javascript">
function ayudaAsignacionResponsable(){         
    var intro = introJs();
      intro.setOptions({
        nextLabel: 'Siguiente',
        prevLabel: 'Anterior',
        skipLabel:'Terminar',
        doneLabel:'Cerrar',
        steps: [      
            {
                element: document.querySelector('.grid-view .filters'),
                intro: "Filtros para realizar busquedas especificas, puede especificar mas de un dato."
            },            
            {
                element: document.querySelector('.cuadroselecion'),
                intro: "Medio pago adoptado."
            },
            
            {
                element: document.querySelector('#drop-menu-grupofamiliar'),
                intro: "Botonera opciones gestionar grupo familiar."
            }, 
            
            {
                element: document.querySelector('.box-alumno-familia'),
                intro: "Detalle de los alumnos cargados a la familia."
            },
            
            {
                element: document.querySelector('.box-responsbale-familia'),
                intro: "Detalle de los responsables cargados a la familia."
            },
            
            {
                element: document.querySelector('#btn-asignar-responsable'),
                intro: "Presione para dar de alta/asignar un nuevo responsable."
            },
            
            
        ]
      });
      intro.start();
}      
</script>