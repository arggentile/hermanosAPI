<?php

namespace app\models;

use Yii;
use \app\models\base\ConvenioPago as BaseConvenioPago;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "convenio_pago".
 */
class ConvenioPago extends BaseConvenioPago
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
               'bedezign\yii2\audit\AuditTrailBehavior'
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                ['fecha_alta', 'date', 'format' => 'php:Y-m-d'],
                ['xfecha_alta', 'date', 'format' => 'php:d-m-Y', 'message'=>'Ingrese una Fecha Valida'],
                ['xfecha_alta','required','message'=>'Ingrese una Fecha Valida.'] 
             ]
        );
    }
    
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                'xfecha_alta' => 'Fecha.Alta',  
                'id'=>'Nro'
            ]
        );
    }
    
    /**************************************************************/
    /**************************************************************/
    public function getXfecha_alta()
    {
        if (!empty($this->fecha_alta) && $valor = Fecha::convertirFecha($this->fecha_alta,"Y-m-d","d-m-Y"))
        {
            return $valor;
        } else {
            return $this->fecha_alta;
        }
    }

    public function setXfecha_alta($value)
    {
        if (!empty($value) && $valor = Fecha::convertirFecha($value,"d-m-Y","Y-m-d"))
        {
            $this->fecha_alta = $valor;
        }else{
            $this->fecha_alta = $value;
        }
    }  

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMiFamilia()
    {
        return $this->hasOne(\app\models\GrupoFamiliar::className(), ['id' => 'id_familia']);
    }    

    /**************************************************************/
    /**************************************************************/
    /**************************************************************/
    /**************************************************************/    
    public function sePuedeEditar(){
        if($this->getCantCuotas() == $this->getCuotasPendientes())
            return true;
        else
            return false;
    }
    public function sePuedeEliminar(){
        if($this->getCantCuotas() == $this->getCuotasPendientes())
            return true;
        else
            return false;
    }
    
    public function getCantCuotas(){
        $convenio = $this->id;             
        $cuotas = CuotaConvenioPago::find()->where("id_conveniopago = ".$convenio)->all();
        return count($cuotas);
    }
    
    public function getCuotasPendientes(){        
        $convenio = $this->id;     
        $abiertas = [EstadoServicio::ID_ABIERTA, EstadoServicio::ID_EN_CONVENIOPAGO, EstadoServicio::ID_EN_DEBITOAUTOMATICO];
        $cuotasVencidas = CuotaConvenioPago::find()
                ->andWhere(["in", "id_estado", $abiertas])->andWhere(["id_conveniopago" => $convenio])->all();
        if(empty($cuotasVencidas)){
             return 0;
        }else{
            return count($cuotasVencidas); 
        }          
    } 
    
    public function getSaldoAbonado(){
        $saldo = 0;
        $abiertas = [EstadoServicio::ID_ABONADA, EstadoServicio::ID_ABONADA_EN_CONVENIOPAGO, EstadoServicio::ID_ABONADA_EN_DEBITOAUTOMATICO];
        $cuotas = CuotaConvenioPago::find()
                ->andWhere(["in", "id_estado", $abiertas])->andWhere(["id_conveniopago" => $this->id])->all();
        if(!empty($cuotas))
            foreach($cuotas as $cuota)
                $saldo += $cuota->monto;
        return $saldo;            
    }
}
