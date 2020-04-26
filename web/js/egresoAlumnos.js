$(document).ready(function(){
    $('#formSearchEgresoAlumnos').on('beforeValidate',function(e){
        $(".btn-submit-envio").attr('disabled','disabled');
        $(".btn-submit-envio").html("<i class=\'fa fa-spinner fa-spin\'></i> Procesando...");        
    });
    
    $("#formSearchEgresoAlumnos").on("afterValidate",function(e, messages){
        if ( $(".form-prev-submit").find(".has-error").length > 0){
            $(".btn-submit-envio").removeAttr("disabled");
            $(".btn-submit-envio").html("<i class=\'fa fa-save\'></i> Guardar...");
        }
    });
});   


$('#egresoalumnoform-es_egreso').on('change',function() { 
    val = $(this).val();
   
    if (val == '1'){ 
        $('#establegreso').css('display','none'); 
        $('#divisionegreso').css('display','none'); 
    }
    if (val== '0'){        
        $('#establegreso').css('display','block'); 
        $('#divisionegreso').css('display','block'); 
       
    }       
});


$('#formEgresoAlumnos').on('beforeSubmit', function (e) {     
    $(".btn-submit-envio").attr('disabled','disabled');
    $(".btn-submit-envio").html("<i class=\'fa fa-spinner fa-spin\'></i> Procesando...");   
        
    e.preventDefault();
    var alumnosSeleccionados = $('#grid-egreso-alumnos').yiiGridView('getSelectedRows').toString();

    if(alumnosSeleccionados.length==0 || alumnosSeleccionados==''){
        $(".btn-submit-envio").removeAttr("disabled");
        $(".btn-submit-envio").html("<i class=\'fa fa-save\'></i> Egresar");
        alert('Seleccione al menos algun alumno');
        return false;
    }
    else{
        var egresaralumnos =  $('#egresaralumnos').val();
        if(egresaralumnos=='1' || egresaralumnos==1){
            return true;
        }else{
            urlForm = $(this).attr('action');

            var estabInicial = $('#establecimiento-egreso-inicial').val();
            var divisionInicial = $('#division-egreso-inicial').val();
            var estabEgreso = $('#alumno-establecimiento option:selected').html();
            var divisionEgreso = $('#alumno-id_divisionescolar option:selected').html();
            var tipoegreso = $('#egresoalumnoform-es_egreso').val();
            
            if(tipoegreso=='1'){
                var mensajeAdvertir = 'Está seguro que desea egresar del Establecimiento los alumnos';
            }
            else{    
                var mensajeAdvertir = 'Está seguro que desea migrar los alumnos de:' + divisionInicial + '('+ estabInicial +')'
                 + ' a la división ' + divisionEgreso + ' (' + estabEgreso + ')?';
            }

            bootbox.confirm({
                message: mensajeAdvertir,
                buttons: {
                    confirm: {
                        label: 'Si',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-danger'

                    }
                },
                callback: function (result) {  
                    if(result===true){                
                        $('#egresaralumnos').val(1);
                        $('#formEgresoAlumnos').submit();
                    }else{
                        $(".btn-submit-envio").removeAttr("disabled");
                        $(".btn-submit-envio").html("<i class=\'fa fa-save\'></i> Egresar");    
                    }
                }
            });     
        return false;
        }
    }
});
    