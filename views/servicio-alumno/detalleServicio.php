<p> 
    <span class="text-light-blue text-bold"> Alumno: </span> 
        <?= $model->alumno->persona->apellido.", ".$model->alumno->persona->nombre; ?> <br />
    <span class="text-light-blue text-bold"> Familia: </span>
        <?=
        $model->alumno->persona->apellido . ", " . 
        $model->alumno->grupofamiliar->apellidos . "(FOLIO: ". $model->alumno->grupofamiliar->folio . ")"
        ?>
    <br />
    <span class="text-light-blue text-bold"> Importe Servicio: </span>
        <?= $model->importe_servicio; ?> <br />
    
    <span class="text-light-blue text-bold"> Importe Descuento: </span>
        <?=$model->importe_descuento;?>  <br />
    
    <?php
    if(!empty($model->bonificacionServicioAlumnos)) {
        echo "Detalle Bonificaciones Aplicadas";
        echo "<ul>";
        foreach($model->bonificacionServicioAlumnos as $bonificacionAplicada){
            echo "<li>" . $bonificacionAplicada->bonificacion->descripcion . "( Valor: " .  $bonificacionAplicada->bonificacion->valor . ")" . "</li>";
        }
        echo "</ul>";
    }
    ?>
        
    <span class="text-light-blue text-bold"> Estado Servicio: </span>
        <?php
        if($model->estado->id== \app\models\EstadoServicio::ID_ABIERTA)
            echo "Abierta/Adeuda";
        else
            echo $model->estado->descripcion;?>  <br />
</p>

    
