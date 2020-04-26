<?php

namespace app\models;

use Yii;
use \app\models\base\DivisionEscolar as BaseDivisionEscolar;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "division_escolar".
 */
class DivisionEscolar extends BaseDivisionEscolar
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
                  # custom validation rules
             ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'id_divisionegreso' => 'Division Egreso',
            ]
        );
        
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'id_establecimiento' => 'Id Establecimiento',
            'iniciales' => 'Iniciales',
            'id_divisionegreso' => 'Id Divisionegreso',
        ];
    }

    
    
    /******************************************************************/
    /******************************************************************/
    public static function getDivisionesEscolares(){
        $dropciones = DivisionEscolar::find()->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'nombre');
    }
    
    public static function getDivisionesEstablecimiento($idEst){
        $dropciones = DivisionEscolar::find()->where('id_establecimiento='.$idEst)->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'nombre');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMiEstablecimiento()
    {
        return $this->hasOne(\app\models\Establecimiento::className(), ['id' => 'id_establecimiento']);
    }
    /*
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMiServicios()
    {
        return $this->hasMany(\app\models\ServicioDivisionEscolar::className(), ['id_divisionescolar' => 'id']);
    }
    

}
