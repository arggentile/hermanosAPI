<?php

namespace app\models;

use Yii;
use \app\models\base\Responsable as BaseResponsable;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "responsable".
 */
class Responsable extends BaseResponsable
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
    
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                'id_tipo_responsable' => 'Tipo Responsable',
            ]
        );
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMiPersona()
    {
        return $this->hasOne(\app\models\Persona::className(), ['id' => 'id_persona']);
    }    
}
