<?php

namespace app\components;

use Yii;
use yii\base\Model;


use app\models\FondoFijo;
use app\models\EgresosFondoFijo;
use app\models\MovimientoFondoFijo;



class FondoFijoComponent extends \yii\base\Component
{
    
    public static function acentarEgresoFondoFijo(\app\models\EgresosFondoFijo $modelEgreso){
        
        try{
            $modelFondoFijo = FondoFijo::findOne($modelEgreso->id_fondofijo);
            if(!$modelFondoFijo)
                throw new NotFoundHttpException('The requested page does not exist.');
            
            $modelFondoFijo->monto_actual-=$modelEgreso->importe;
            
            $modelMovimientoFondo = new MovimientoFondoFijo();
            $modelMovimientoFondo->id_fondofijo = $modelFondoFijo->id;
            $modelMovimientoFondo->tipo_movimiento = \app\models\TipoMovimientoCuenta::IDtipo_moviento_egreso;            
            $modelMovimientoFondo->id_tipopago = \app\models\FormaPago::PAGO_ESFECTIVO;
            $modelMovimientoFondo->importe = $modelEgreso->importe;
            $modelMovimientoFondo->xfecha_realizacion = date('d-m-Y');
            $modelMovimientoFondo->id_egreso = $modelEgreso->id;
            
            if($modelMovimientoFondo->save() && $modelFondoFijo->save())
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


    public static function actualizarEgresoFondoFijo(\app\models\EgresosFondoFijo $modelEgreso){
        
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
    
    public static function eliminarEgresoFondoFijo(\app\models\EgresosFondoFijo $modelEgreso) {
        
        try{ 
            $movimientoFondoActaul = MovimientoFondoFijo::find()->where(['id_egreso'=>$modelEgreso->id])->one();
            if(!$movimientoFondoActaul)
                throw new NotFoundHttpException('The requested page does not exist.');
            
            if($movimientoFondoActaul->importe != $modelEgreso->importe)
                throw new NotFoundHttpException('Los importe del egreso y elmovimien del fondo no coinciden.');
            
            $modelFondoFijo = FondoFijo::findOne($modelEgreso->id_fondofijo);
            if(!$modelFondoFijo)
                throw new NotFoundHttpException('The requested page does not exist.');
            
            $modelFondoFijo->monto_actual += $modelEgreso->importe;
            
            if($modelFondoFijo->save() && $movimientoFondoActaul->delete() && $modelEgreso->delete()  )
                return true;
            else{
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