<div class="row" id="informacionestablecimiento"> <!-- row dettales delconvenio -->
    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
        <table>
            <tr>
                <td width="25%"> 
                    <img class="img-responsive" src="<?php echo Yii::getAlias('@web') . "/images/escuela.png"; ?>" alt="cp_dollar" />  
                </td>
                <td width="60%">
                    <h3 class="text-light-blue text-bold"> <?php echo $model->nombre; ?> </h3>
                    <span class="text-light-blue text-bold">  Fecha Apertura: </span> <?php echo \Yii::$app->formatter->asDate($model->fecha_apertura); ?><br /> 
                    <span class="text-light-blue text-bold">  Dirección: </span> <?php echo $model->calle; ?> <br />
                    <span class="text-light-blue text-bold">  Teléfono: </span> <?php echo $model->telefono; ?> <br />
                    <span class="text-light-blue text-bold">  Nivel Educativo: </span> <?php echo $model->nivel_educativo; ?> <br />
                </td>
            </tr>                  
        </table>          
    </div>
</div> <!-- fin row dettales del establecimiento -->