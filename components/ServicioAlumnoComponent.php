<?php

namespace app\components;

use Yii;
use yii\base\Model;

use app\models\ServicioOfrecido;
use app\models\ServicioAlumno;
use app\models\ServicioEstablecimiento;
use app\models\CategoriaBonificacion;
use app\models\BonificacionServicioAlumno;


class ServicioAlumnoComponent extends \yii\base\Component
{

    /*
     * Elimina un determinado ServioAlumno siguiendo una logicoa de eliminacion con 
     * saldo abonado igual a cero. Remueve las bonificaciones sobre el servicio en el caso de 
     * que existieran.
     * 
     * Si se parametriza y fuerza a su eliminacion se obvia la restriccion de que el saldo 
     * abonado sea nulo.
     */
    public static  function eliminarSevicioAlumno($idServicioAlumno, $forceDelete=false){           
        try{
            $modelServicioAlumno = ServicioAlumno::findOne($idServicioAlumno);
            if(!$modelServicioAlumno)
                throw new \Exception('Servicio Alumno inexistente.');
            
            if (($modelServicioAlumno->id_estado == \app\models\EstadoServicio::estadoServicio_ABIERTA)
                    &&   ($modelServicioAlumno->importe_abonado == 0) ){
               $bonificacionesServicioAlumno = $modelServicioAlumno->bonificacionServicioAlumnos;
               
               $valid = true;
               if(!empty($bonificacionesServicioAlumno)){
                foreach($bonificacionesServicioAlumno as $bonificacion)
                    $valid = $valid && $bonificacion->delete();
                }
                if(!$valid)
                    \Yii::$app->getModule('audit')->data('errorAction', json_encode($bonificacion->getErrors()));     
               
                if($valid && $modelServicioAlumno->delete())
                    return true;
                else {
                    \Yii::$app->getModule('audit')->data('errorAction', json_encode($modelServicioAlumno->getErrors())); 
                    return false;
                }
            }
        }catch (\Exception $e) {
            \Yii::$app->getModule('audit')->data('errorAction', json_encode($e)); 
            throw new \Exception($e->getMessage());            
        }
    }
    
    
    
    
    /************************************************************/
    /*
     * Funcion que se encarga de dado un identificador de cliente (familia);
     * devulelve todos los servicios asociados al mismo; que esten sin abonar o 
     *  sin asociar a ningun convenio de pago
     */
    public static function DevolverServiciosImpagosLibres($familia){ 
        $query = \app\models\ServicioAlumno::find();        
        $query->joinWith('miAlumno a');
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder'=>'id desc'],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        
        $query->andFilterWhere([
            'a.id_grupofamiliar' => $cliente]);        
            $query->andFilterWhere(['=', 'id_estado', \app\models\EstadoServicio::estadoServicio_ABIERTA]);

        return $dataProvider;
    }
    
    
    /*
     * Funcion que se encarga de dado un identificador de cliente (familia);
     * devulelve todos los servicios asociados al mismo; que esten sin abonar o 
     *  sin asociar a ningun convenio de pago
     * 
     * @params $familia   - identificador de la familia cliente
     * @params $servicios - array id de servicios a excluir
     */
    public static function DevolverServiciosImpagosLibresFiltroServicios($familia,array $servicios = []){ 
        $query = \app\models\ServicioAlumno::find();        
        $query->joinWith('miAlumno a');
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder'=>'id desc'],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        
        $query->andFilterWhere(['a.id_grupofamiliar' => $familia]);
        $query->andFilterWhere(['=', 'id_estado', \app\models\EstadoServicio::estadoServicio_ABIERTA]);
        
        if(!empty($servicios))
            $query->andFilterWhere(['not in', ServicioAlumno::tableName(). '.id' ,  $servicios]);

        return $dataProvider;
    }
    
    
    /*
     * Retorna un query conteniendo los servicios impagos de una dterminado grupo familiar.
     * El query presenta tanto los servicios impagos; como las cuotas impagas
     * de los convenios de pago
     */
    public function devolverDeudaFamilia($familia){
        $query = "SELECT sa.id as nroservicio, id_alumno as idalumno, importe_servicio as montoservicio, 
                                 importe_descuento,importe_abonado, 'SERVICIOS' as tiposervicio 
                            FROM servicio_alumno sa 
                             INNER JOIN alumno a ON (a.id = sa.id_alumno)
                             WHERE sa.id_estado='A' and  a.id_grupofamiliar=$familia
                        ";

        $queryCount = "SELECT COUNT(*) FROM ($query) as total";
        $queryCount= \Yii::$app->db->createCommand($queryCount)->queryScalar();

        $serviciosImpagos = new \yii\data\SqlDataProvider([
            'sql' => $query,   
            'key'=>'nroservicio',
            'totalCount' => $queryCount,
            'pagination' => [
                'pageSize' => 3,
            ],
            'sort' => [
                'attributes' => ['nroservicio', 'idalumno', 'montoservicio','importe_descuento','importe_abonado','tiposervicio'],
            ],                    
        ]);   
        
        return $serviciosImpagos;
    }
    
    
    
    public static function exportServicioAlumnoServicioOfrecido(){
        
    }
    
}