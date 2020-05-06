<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\ServicioEstablecimiento */ 
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <div class="col-sm-12">
        <!-- solo para la ayuda -->
        <?php                 yii\widgets\Pjax::begin([
           'id'=>'pjax-divisionesservicios',
           'enablePushState' => false, 
           'timeout'=>false
           ]); 
       ?>    
       <?=
        yii\grid\GridView::widget([
                   'id' => 'grid-divisiones',
                   'dataProvider' => $dataProviderDivisiones,
                   'columns' => [
                       [
                           'label' => 'Establecimiento',                                    
                           //'filter'=> dmstr\helpers\Html::activeInput('text', $modelPersona,'apellido',['class'=>'form-control']),
                           'value' => function($model) {
                               return $model->establecimiento->nombre;
                           },
                       ],
                       'nombre',
                       ['class' => 'yii\grid\ActionColumn',
                           'template' => '{asignar} {quitar}',
                           'headerOptions' => ['class' => 'actions-ser'],
                           'buttons' =>
                           [
                           'asignar' => function ($url, $model) use ($serviciosDivisiones, $modelServicioOfrecido){
                                   if(!in_array($model->id,$serviciosDivisiones))
                                       return Html::button( '<i class="glyphicon glyphicon-ok-circle"></i>',                            
                                           ['class' => in_array($model->id, $serviciosDivisiones)?'btn btn-xs btn-primary btn-asignar btn-asign-div-ser disabled':'btn btn-asign-div-ser btn-xs btn-primary btn-asignar',
                                            'onclick' => 'js:asignarDivision("'.Url::to(['/servicio-ofrecido/asignar-servicio-division', 'division' => $model->id, 'servicio' => $modelServicioOfrecido->id]) .'");']
                               );  
                           },      
                           'quitar' => function ($url, $model) use ($serviciosDivisiones, $modelServicioOfrecido) {      
                               if(in_array($model->id,$serviciosDivisiones))
                                   return Html::button( '<i class="glyphicon glyphicon-remove-circle"></i>',                            
                                       ['class' => "btn btn-xs btn-danger btn-asign-div-ser btn-quitar  ". in_array($model->id, $serviciosDivisiones)?'btn btn-xs btn-asign-div-ser btn-danger btn-quitar ':'btn btn-xs btn-asign-div-ser btn-danger btn-quitar disabled',
                                           'onclick' => 'js:asignarDivision("'.Url::to(['/servicio-ofrecido/quitar-servicio-division', 'division' => $model->id, 'servicio' => $modelServicioOfrecido->id]) .'");']
                                       );
                                   },
                           ],
                       ],
                   ],
               ]);
           ?>
           <?php yii\widgets\Pjax::end(); ?> 
         
        <input type="hidden" name="urlreloadservicios" id="urlreloadservicios" value="<?= Url::to(['/servicio-ofrecido/asociar-division','id'=>$modelServicioOfrecido->id]); ?>" />
    </div>
</div>
<?php
$this->registerJs("
    
function asignarDivision(xhref){      
    $('body').loading({message: 'AGUARDE... procesando.'}); 
    
    $('.btn-asign-div-ser').attr('disabled','disabled');

    urlreload = $('#urlreloadservicios').val();
    $.ajax({
        url    : xhref,                   
        dataType: 'json',
        success: function (response){                         
            if(response.error==0){
                reportarNotificacionGral('Asignación correcta', 'success', true);   
                $.pjax({container: '#pjax-divisionesservicios', timeout: false, push: false,  replace:false, url: urlreload}).done(function(){
                    $('body').loading('stop');       
                    $('.btn-asign-div-ser').removeAttr('disabled');
                });                            
            }else{
                reportarNotificacionGral('No se pudo realizar la asignación', 'error', true);
                $('body').loading('stop');
                $('.btn-asign-div-ser').removeAttr('disabled');
            }
            
        },
        error: function(xhr){
            $('.btn-asign-div-ser').removeAttr('disabled');
            reportarNotificacionGral(xhr.responseText, 'error', true);   
        }
    });
    
    return false;       
} //fin deleteAjax 

",
 yii\web\View::POS_READY,'multiselect');?>
