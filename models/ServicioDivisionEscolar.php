<?php

namespace app\models;

use Yii;
use \app\models\base\ServicioDivisionEscolar as BaseServicioDivisionEsclar;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "servicio_divisionescolar".
 */
class ServicioDivisionEscolar extends BaseServicioDivisionEsclar
{

    public $divisiones;
    public $establecimiento;
    
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
               ['divisiones','safe'],
               ['establecimiento','safe']# custom validation rules
             ]
        );
    }
    
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                
                'id_servicio' => 'Servicio',                
            ]
        );
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMiDivisionescolar()
    {
        return $this->hasOne(\app\models\DivisionEscolar::className(), ['id' => 'id_divisionescolar']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMiServicio()
    {
        return $this->hasOne(\app\models\ServicioOfrecido::className(), ['id' => 'id_servicio']);
    }
    
    public static function getServiciosxEstablecimiento($idEst){
        try{
            $return = '';
            
            $servicios = ServicioOfrecido::find()
                    ->joinWith(['servicioDivisionescolars serdiv','servicioDivisionescolars.divisionescolar.establecimiento est'])
                    ->where(['est.id' => $idEst])
                    ->orderBy('id')->asArray()
                    ->all();   
            
            
            $opciones = []; 
            if(!empty($servicios))
                foreach($servicios as $drop){
                    $opciones[$drop['id']] = $drop['nombre'];
                };
            return $opciones;
            exit;
        }catch(\Exception $e){    
            \Yii::$app->getModule('audit')->data('errorAction', \yii\helpers\VarDumper::dumpAsString($e)); 
            echo "<option value=''></option>";
        }
        
        
       
        
    }
}
