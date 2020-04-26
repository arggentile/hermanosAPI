<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;




/* @var $this yii\web\View */
/* @var $model common\models\GrupoFamiliar */

$this->title = "Grupo Familiar: " . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Grupo Familiars', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-primary box-colegio" id="grupo-familiar-index">
    <div class="box-header with-border">
        <i class="fa fa-users"></i> <h3 class="box-title"> Familia Nº <?= $model->folio; ?> <?= $model->apellidos;?> </h3>       
    </div>
    
    <div class="box-body">
        <?php echo $this->render('_viewDatosFamilia', ['model' => $model]); ?>
        
        <?php echo $this->render('_viewIntegrantes', 
                            ['dataProviderResponsables' => $dataProviderResponsables,
                             'dataProviderAlumnos' => $dataProviderAlumnos,
                             'familia'=>$model->id   
                            ]); ?>
        
    </div> <!-- box body -->
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
                element: document.querySelector('.box-alumno-familia'),
                intro: "Detalle de los alumnos cargados a la familia."
            },
            
            {
                element: document.querySelector('.box-responsbale-familia'),
                intro: "Detalle de los responsables cargados a la familia."
            },
            
            {
                element: document.querySelector('#btn-asignar-responsable'),
                intro: "Presione para dar de asignar un responsable ya dado de alta en el sistema. Verifique que no este dado de alta previamente"
            },
            {
                element: document.querySelector('#btn-alta-responsable'),
                intro: "Alta de un nuevo responsable."
            },
            
            
        ]
      });
      intro.start();
}      
</script>
