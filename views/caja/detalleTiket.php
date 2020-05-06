<div class="box box-solid box-colegio">
    <div class="box-header with-border">
        <i class="fa fa-arrow-circle-o-right"></i><h3 class="box-title"> Detalle Comprobante Pago :  </h3>
    </div>
    <div class="box-body">
        <br />        
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2 col-xs-12">

            <table>
                <tr>
                    <td width="25%"> <img class="img-responsive" src="<?php echo Yii::getAlias('@web') . "/images/cajaFacturas.png"; ?>" alt="cp_dollar" />  </td>
                    <td style="padding-left: 20px;">
                        <span class="text-light-blue bold" style="font-size: 22px; color: #a00519 !important; font-weight: bold;"> Tiket Nro:  <?php echo $modelTiket->id; ?> </span><br />
                        <span class="text-black bold"  style="font-size: 18px; font-weight: bold;">    Fecha Tiket:  </span><?php echo \Yii::$app->formatter->asDate($modelTiket->fecha_tiket); ?> <br />
                        <span class="text-black bold"  style="font-size: 18px; font-weight: bold;">    Monto: </span> <?php echo "$ ".$modelTiket->importe; ?> <br />  
                        
                        <br/>
                        <span class="text-black bold"  style="font-size: 18px; font-weight: bold;"> Familia: </span> 
                            <?= (!$modelTiket->grupoFamiliar)?$modelTiket->grupoFamiliar->apellidos ."  FOLIO: ". $modelTiket->grupoFamiliar->folio:''; ?> </span>                        
                        <br />
                        
                        <span class="text-black bold"  style="font-size: 18px; font-weight: bold;"> Factura Afip: </span> <br />
                        
                        <?php
                        if($modelFactura === null || (!$modelFactura && $modelFactura->informada=='0')){                            
                            echo "Debido a un error no pudo generarse la factura. ";
                            echo dmstr\helpers\Html::button('<i class="glyphicon glyphicon-print"> </i> Generar Factura', 
                                    ['class' => 'btn btn-warning', 
                                    'id'=>'btn-pdf-tiket',
                                    'onclick'=>'js:{generarFactura("'. yii\helpers\Url::to(['/caja/generar-factura', 'idTiket'=>$modelTiket->id]) .'");}']);
                        
                            
                        }else{ 
                        ?>
                            <span class="text-black bold"  style="font-size: 18px; font-weight: bold;"> Factura Afip: </span> 
                            <?= "Nº: ". $modelFactura->nroFactura ."  CAE: ". $modelFactura->cae .
                               dmstr\helpers\Html::button('<i class="glyphicon glyphicon-print"> </i> Tiket', ['class' => 'btn btn-warning', 'id'=>'btn-pdf-tiket',
                                'onclick'=>'js:{downFactura("'. yii\helpers\Url::to(['/caja/pdf-tiket','idTiket'=>$modelTiket->id]) .'");}']);   
                                                        
                        } ?>
                            
                        
                        
                           
                    </td>
                </tr>
                
               
            </table>
                
            
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <?php
                if(!$serviciosTiket){
                    echo "Detalles: " . $modelTiket->detalles;
                }
                ?>
            </div>
        </div>



    </div>
</div>

<script type="text/javascript">  
function downFactura(xhref){
    $("body").loading({message: 'ESPERE... procesando'});
    $.ajax({
        url    : xhref,
        type   : "post",            
        dataType: "json",
        success: function (response){             
             $("body").loading('stop');  
             if(response.result_error==='0'){
                window.location.href = response.result_texto; 
             }else{
                new PNotify({
                    title: 'Error',
                    text: response.message,
                    icon: 'glyphicon glyphicon-envelope',
                    type: 'error'
                });
             }
        },
                error: function(XHR) {
                   $("body").loading('stop');                    
                   if (XHR.statusText == 'Unauthorized')
                    {
                        new PNotify({
                            title: 'ERROR!!!',
                            text: 'USTED NO TIENE LOS PERMISOS SUFICIENTES PARA LLEVAR A CABO LA TAREA SOLICITADA',
                            icon: 'ui-icon ui-icon-mail-closed',
                            opacity: .8,
                            type: 'success'
                           
                        });
                    }else
                    if( ((XHR.status == '403') || ((XHR.status == 403))) && ((XHR.statusText == 'Forbidden'))){
                            new PNotify({
                                title: 'ERROR!!!',
                                text: 'USTED NO TIENE LOS PERMISOS SUFICIENTES PARA LLEVAR A CABO LA TAREA SOLICITADA',
                                icon: 'ui-icon ui-icon-mail-closed',
                                opacity: .8,
                                type: 'success'                           
                            });  
                     }
                }
    }); 
}

function enviarFactura(xhref){
    $("body").loading({message: 'ESPERE... procesando'});
    $.ajax({
        url    : xhref,
        type   : "post",            
        dataType: "json",
        success: function (response){             
             $("body").loading('stop');  
             if(response.result_error==='0'){
                 new PNotify({
                    title: 'OK',
                    text: 'Se envio por correo el comprobante de pago',
                    icon: 'glyphicon glyphicon-envelope',
                    type: 'success'
                });
             }else{
                new PNotify({
                    title: 'Error',
                    text: 'No se pudo enviar el comprobante de pago. No se encuentra registrado el mail del Matrículado',
                    icon: 'glyphicon glyphicon-envelope',
                    type: 'error'
                });
             }
        },
                error: function(XHR) {
                   $("body").loading('stop');                    
                   if (XHR.statusText == 'Unauthorized')
                    {
                        new PNotify({
                            title: 'ERROR!!!',
                            text: 'USTED NO TIENE LOS PERMISOS SUFICIENTES PARA LLEVAR A CABO LA TAREA SOLICITADA',
                            icon: 'ui-icon ui-icon-mail-closed',
                            opacity: .8,
                            type: 'success'
                           
                        });
                    }else
                    if( ((XHR.status == '403') || ((XHR.status == 403))) && ((XHR.statusText == 'Forbidden'))){
                            new PNotify({
                                title: 'ERROR!!!',
                                text: 'USTED NO TIENE LOS PERMISOS SUFICIENTES PARA LLEVAR A CABO LA TAREA SOLICITADA',
                                icon: 'ui-icon ui-icon-mail-closed',
                                opacity: .8,
                                type: 'success'                           
                            });  
                     }
                }
    }); 
}
</script>