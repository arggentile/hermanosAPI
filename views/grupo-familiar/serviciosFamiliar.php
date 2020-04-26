<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;

use app\assets\GrupoFamiliarAsset;
GrupoFamiliarAsset::register($this); 


/* @var $this yii\web\View */
/* @var $model common\models\GrupoFamiliar */

$this->title = "Grupo Familiar: " . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Grupo Familiars', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-solid box-colegio" id="grupo-familiar-index">
    <div class="box-header with-border">
        <i class="fa fa-users"></i> <h3 class="box-title"> FAMILIA Nº <?= $model->id;?> </h3>       
    </div>
    
    <div class="box-body">
        <?php echo $this->render('_viewDatosFamilia', ['model' => $model]); ?>
    
        <div class="row">
            <!--col alumnos-->
            <div class="col-xs-12">
                <div class="box box-greenlightsite box-alumno-familia" id="grupo-familiar-index">
                    <div class="box-header with-border">
                        <i class="fa fa-users"></i> 
                            <h3 class="box-title"> Servicios Adheridos </h3> 
                          
                    </div>
                    <div class="box-body">
                        <?= app\widgets\buscadorServiciosAlumno\BuscadorServiciosAlumno::widget(
                        [   'searchModel' => $searchModel,
                            'dataProvider'=>$dataProvider,
                            'buscador'=>true,
                            'notDisplaySearch'=>['familia','alumno','establecimiento'],
                            ]);                        
                        ?>
                    </div>
                </div>
            </div>
        </div>
                
    </div>
</div>
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
                intro: "Visualizacion y gestion Grupo Familiar."
            },  
            {
                element: document.querySelector('#datafamiliar'),
                intro: "Datos basicos de la familia."
            }, 
            {
                element: document.querySelector('#drop-menu-grupofamiliar'),
                intro: "Botonera opciones gestionar grupo familiar."
            }, 
            
            {
                element: document.querySelector('#reporte-servicios-alumno'),
                intro: "Servicios asociados a la familia."
            },
            
            
            
            
            
            
            
        ]
      });
      intro.start();
}      
</script>
