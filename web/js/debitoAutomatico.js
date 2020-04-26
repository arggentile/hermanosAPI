/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function downArchivoBanco(xhref){       
    $('body').loading({message: 'ESPERE... procesando'});
    $.ajax({
         url    : xhref,
         type   : 'post',            
         dataType: 'json',
         success: function (response){ 
            $('body').loading('stop');  
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
                   $('body').loading('stop');                    
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


$('#btn-verificar').on('click',function(){
    $('body').loading({message: 'ESPERE... procesando', theme:'dark'});
    $.ajax({
        url    : $(this).attr('value'),
        type   : 'post',            
        dataType: 'json',
        success: function (response){
            $('body').loading('stop');  
            if(response.result_error=='0'){
                window.location.href = response.result_texto; 
            }else{
                new PNotify({
                    title: 'Error',
                    text: response.message,
                    icon: 'glyphicon glyphicon-envelope',
                    type: 'error'
                });
            }
        }     
    }); 
});    

/*procesar debolucion*/
$('#btn-procesa').on('click',function(){               
    $('#modalProcesa').modal('show').find('#modalContent').load(jQuery(this).attr('value'));
}); 


