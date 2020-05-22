<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

use app\assets\GrupoFamiliarAsset;
GrupoFamiliarAsset::register($this);
?>
<?php
    yii\bootstrap\Modal::begin([        
        'id'=>'modalAsignacionResponsable',
        'header'=>'Asignación de Responsable.',
        'options' => ['class'=>'modalSite'] , 
        'size' => 'modal-lg',
        ]);
        echo "<div id='modalContent'></div>";
    yii\bootstrap\Modal::end();
?>

<div class="row">
    <!--col alumnos-->
    <div class="col-xs-6">
        <div class="box box-greenlightsite box-alumno-familia" id="grupo-familiar-index">
            <div class="box-header with-border">
                <i class="fa fa-users"></i> 
                    <h3 class="box-title"> Alumnos / Hijos </h3>  
                    <div class="box-tools pull-right">
                        <?php
                        if(Yii::$app->user->can('cargarAlumno')){
                            echo Html::a('<i class="fa fa-plus"></i> ', ['/alumno/empadronamiento','familia'=>$familia], ['class'=>'btn btn-primary btn-xs']); 
                        }   
                        ?>
                    </div>
            </div>
            <div class="box-body">
                <?php
                
                Pjax::begin([
                        'id' => 'pjax-alumnos',
                        'enablePushState' => false,
                        'timeout' => false
                    ]);

                $alumnos = $dataProviderAlumnos->getModels();
                if(count($alumnos)>0){
                ?>
                <ul class="todo-list todo-list-alumnos">                    
                    <?php foreach($alumnos as $alumno){
                            if(Yii::$app->user->can('visualizarAlumno'))
                                echo "<li>". Html::a('<i class="fa fa-eye"></i> '. $alumno->miPersona->apellido ."; ".$alumno->miPersona->nombre, ['alumno/view', 'id' => $alumno->id], []);
                            else
                                    echo "<li>". $alumno->miPersona->apellido ."; ".$alumno->miPersona->nombre;
                            
                            echo '<div class="tools tools-alumnos">';
                            if(Yii::$app->user->can('cargarAlumno')){
                                echo Html::a('<i class="fa fa-edit"></i>', ['/alumno/empadronamiento', 'id' => $alumno->id], []);
                            }
                            echo " ";
                            if(Yii::$app->user->can('eliminarAlumno')){
                                echo Html::a('<i class="fa fa-trash-o"></i>', 'javascript:void(0)', [
                                            'data-url' =>  Url::to(['/alumno/delete', 'id' => $alumno->id]),
                                            'class' => 'btn-delete-alumno'
                                        ]);
                            }
                            echo "</li>";
                        }
                    }?>
                </ul>
                <?php Pjax::end(); ?> 
            </div> <!-- box body -->
        </div>
    </div>
    
    <!-- col responsbales -->
    <div class="col-sm-6">
        <div class="box box-greenlightsite box-responsable-familia">
            <div class="box-header with-border">
                <i class="fa fa-users"></i> <h3 class="box-title"> Integrantes </h3> 
                <div class="box-tools pull-right">
                    <?php
                    
                    if(Yii::$app->user->can('gestionarResponable')){
                        echo 
                            Html::button('<i class="fa fa-share-square-o"></i> Asignar', 
                                ['value' => Url::to(['/grupo-familiar/asignar-responsable', 'familia' => $familia]), 
                                 'class' => 'btn btn-default btn-xs', 'id' => 'btn-asignar-responsable']);
                        echo " ";
                        echo Html::button(' <i class="fa fa-plus-square"></i> Cargar', 
                                ['value'=> Url::to(['/grupo-familiar/carga-responsable', 'idFamilia'=>$familia]),
                                 'class' => 'btn btn-primary btn-xs','id'=>'btn-alta-responsable',
                                 'onclick'=>'js:{cargarResponsable(this);}'    
                                ]); 
                    } ?>
                </div>
               </div>
            <div class="box-body">
                <?php
                Pjax::begin(['id' => 'pjax-responsables',
                    'enablePushState' => false,
                    'timeout' => false]);

                $responsables = $dataProviderResponsables->getModels();
                if(count($responsables)>0){?>
                <ul class="todo-list todo-list-responsables">    
                    <?php
                    foreach($responsables as $responsable){             
                        echo "<li>";
                        if($responsable->cabecera=='1')
                            echo "<span class='label label-primary' tooltip='Responsable' title='Responsable'>R</span>";
                        
                        echo " <span class='label label-warning'>". $responsable->tipoResponsable->nombre . "</span> ". $responsable->miPersona->apellido .
                                "; ".$responsable->miPersona->nombre;
                        if(Yii::$app->user->can('gestionarResponable')){
                            echo "<div class='tools tools-responsables'>";
                            echo    Html::a('<i class="fa fa-edit"></i>', 'javascript:void(0)' ,[
                                    'class' => '',
                                    'onclick' => 'js:actualizarResponsable("' . Url::to(['/grupo-familiar/actualizar-responsable', 'id' => $responsable->id]) . '");']);
                            echo " ";
                                    echo    Html::a('<i class="fa fa-trash-o"></i>', 'javascript:void(0)',[
                                    'class' => '',
                                    'onclick' => 'js:quitarResponsable("' . Url::to(['/grupo-familiar/quitar-responsable', 'id' => $responsable->id]) . '");']);   
                            echo "</div></li>";
                        }
                    }
                    echo "</ul>";
                }

                ?>
                <?php Pjax::end(); ?> 
            </div> <!-- box body -->
        </div>
    </div>
</div>
    
<input type="hidden" name="familia" id="familia" value="<?= $familia; ?>" />