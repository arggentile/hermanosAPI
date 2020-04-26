<?php

namespace app\components;

use Yii;
use yii\base\Model;


use app\models\Persona;
use app\models\Establecimiento;
use app\models\DivisionEscolar;



class EstablecimientoService extends \yii\base\Component
{
    
    public static function eliminarEstablecimiento($idEstablecimiento){
        
        try{
            $model = Establecimiento::findOne($idEstablecimiento);
            if(!$model)
                throw new HttpException(null, 'No se encontró el establecimiento a eliminar');
            
            if(!$model->delete())
                throw new HttpException(null, 'No se pudo eliminar el establecimiento');
            
            $response['succes'] = true;
            $response['mensaje'] = 'Eliminación correcta';
            return $response;            
        }catch (\Exception $e) { 
            var_dump($e->getMessage());
            \Yii::$app->getModule('audit')->data('errorAction', json_encode($e));  
            
        }        
    }


    public static function cargarEstablecimiento(Establecimiento $modelEgreso){
        
        try{
            $modelEgresoActaul = EgresosFondoFijo::findOne($modelEgreso->id);
            if(!$modelEgresoActaul)
                throw new NotFoundHttpException('The requested page does not exist.');
            
            $movimientoFondoActaul = MovimientoFondoFijo::find()->where(['id_egreso'=>$modelEgreso->id])->one();
            if(!$movimientoFondoActaul)
                throw new NotFoundHttpException('The requested page does not exist.');
            
            if($movimientoFondoActaul->importe != $modelEgresoActaul->importe)
                throw new NotFoundHttpException('Los importe del egreso y elmovimien del fondo no coinciden.');
            
            $modelFondoFijo = FondoFijo::findOne($modelEgreso->id_fondofijo);
            if(!$modelFondoFijo)
                throw new NotFoundHttpException('The requested page does not exist.');
            
            $importeAnterior = $modelEgresoActaul->importe;
            
            $modelFondoFijo->monto_actual= $modelFondoFijo->monto_actual + $modelEgreso->importe - $importeAnterior;
            
            $movimientoFondoActaul->importe = $modelEgreso->importe;
            $movimientoFondoActaul->xfecha_realizacion = date('d-m-Y');
            
            if($modelEgreso->save() && $movimientoFondoActaul->save() && $modelFondoFijo->save())
                return true;
            else{
                var_dump($modelMovimientoFondo->getErrors());                
                var_dump($modelFondoFijo->getErrors());
                \Yii::$app->getModule('audit')->data('errorRegristrandoEgresoFondoFijo', json_encode($modelMovimientoFondo->getErrors()));
                \Yii::$app->getModule('audit')->data('errorRegristrandoEgresoFondoFijo', json_encode($modelFondoFijo->getErrors()));
                return false;                
            }        
                
        }catch (\Exception $e) { 
            var_dump($e->getMessage());
            \Yii::$app->getModule('audit')->data('errorAction', json_encode($e));  
            
        }        
    }
    
}