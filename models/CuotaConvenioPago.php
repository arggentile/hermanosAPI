<?php

namespace app\models;

use Yii;
use \app\models\base\CuotaConvenioPago as BaseCuotaConvenioPago;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cuota_convenio_pago".
 */
class CuotaConvenioPago extends BaseCuotaConvenioPago
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
                ['fecha_establecida', 'date', 'format' => 'php:Y-m-d'],
                ['xfecha_establecida', 'date', 'format' => 'php:d-m-Y', 'message'=>'Ingrese una Fecha Valida'],
                ['fecha_establecida', 'rulesFechaCuotaValida'],
             ]
        );
    }
    
    /**************************************************************/
    /**************************************************************/
    public function getXfecha_establecida()
    {
        if (!empty($this->fecha_establecida) && $valor = Fecha::convertirFecha($this->fecha_establecida,"Y-m-d","d-m-Y"))
        {
            return $valor;
        } else {
            return $this->fecha_establecida;
        }
    }

    public function setXfecha_establecida($value)
    {
        if (!empty($value) && $valor = Fecha::convertirFecha($value,"d-m-Y","Y-m-d"))
        {
            $this->fecha_establecida = $valor;
        }else{
            $this->fecha_establecida = $value;
        }
    }
    
    public function rulesFechaCuotaValida() {
        if(!empty($this->fecha_establecida)){
            if(Fecha::esFechaMayor($this->fecha_establecida, date('Y-m-d'))){
                $this->addError('xfecha_establecida', 'La fecha debe ser mayor a la fecha Actual.');
                $this->addError('fecha_establecida', 'La fecha debe ser mayor a la fecha Actual.');
            }
        }
    }    
    
    /**************************************************************/
    /**************************************************************/
    public function getDescripcionEstadoCuota(){
        $return = '';
        switch ($this->id_estado) {
            case EstadoServicio::ID_ABIERTA:
                $return = "<span class='label label-success'>SIN PAGAR</span>";
                break;
            case EstadoServicio::ID_ABONADA:
                $return = "<span class='label label-warning'>PAGADA</span>";
                break;
            case EstadoServicio::ID_EN_DEBITOAUTOMATICO:
                $return = "<span class='label label-success'>ENVIADA DEBITO AUTOMATICO</span>";
                break;
            case EstadoServicio::ID_ABONADA_EN_DEBITOAUTOMATICO:
                $return = "ABONADA CON DEBITO AUTOMATICO";
                break;
        }
        return $return;
    }
    /*public function getEsCuotaPagada(){
        if($this->pagada=='1'){
            return "SI";
        }else{
             return "NO";
        }           
    }   */ 
    
/*        switch ($this->estado) {
            case 'A':
                return "<span class='label label-success'>SIN PAGAR</span>";
                break;
            case '0':
                return "<span class='label label-success'>SIN PAGAR</span>";
                break;
            case 'PA':
                return "<span class='label label-warning'>PAGADA</span>";
                break;
            case 'DA':
                return "<span class='label label-success'>ENVIADA DEBITO AUTOMATICO</span>";
                break;
            case 'DA / PA':
                return "ABONADA CON DEBITO AUTOMATICO";
                break;
            case 'CP':
                return "<span class='label label-success'>EN CONVENIO DE PAGO</span>";
                break;
        }            
    }
    */
    public function getImporteRestante(){
        $importe = $this->monto -  $this->importe_abonado;
        return $importe;
    }
    
    public  function getDetalleDatos(){
        return "CP Nº: ". $this->id_conveniopago . " Nº Cuota: " .$this->nro_cuota;    
        
    }
 
    public static  function getDetalleDatosCuota($idCuota){
        $modelCuota = self::findOne($idCuota);
        return "CP Nº: ". $modelCuota->id_conveniopago . " Nº Cuota: " . $modelCuota->nro_cuota;    
        
    }
    
    /************************************************************/
    /*
     * Funcion que se encarga de dado un identificador de cliente (abogado);
     * devuleve todos los servicios asociados al aogado que esten sin pagar 
     *  sin asociar a nin convenio de pago
     *//*
    public static function DevolverCuotasImpagas($cliente){ 
        $query = CuotaConvenioPago::find();
        $query->joinWith('idConveniopago cp');
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder'=>'id desc'],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        
        $query->andFilterWhere(['cp.id_abogado' => $cliente]);        
        $query->andFilterWhere(['=', 'pagada', '0'])
              ->andFilterWhere(['=', 'estado', 'A']);  

        return $dataProvider;
    }
    */

}
