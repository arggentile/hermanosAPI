<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "debito_automatico_registro".
 *
 * @property integer $id
 * @property integer $id_debitoautomatico
 * @property integer $id_familia
 * @property string $monto
 * @property string $resultado
 * @property boolean $correcto
 *
 * @property \app\models\DebitoAutomatico $debitoautomatico
 * @property \app\models\GrupoFamiliar $familia
 * @property string $aliasModel
 */
abstract class DebitoAutomaticoRegistro extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'debito_automatico_registro';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_debitoautomatico', 'id_familia', 'monto'], 'required'],
            [['id_debitoautomatico', 'id_familia'], 'integer'],
            [['monto'], 'number'],
            [['correcto'], 'boolean'],
            [['resultado'], 'string', 'max' => 255],
            [['id_debitoautomatico'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\DebitoAutomatico::className(), 'targetAttribute' => ['id_debitoautomatico' => 'id']],
            [['id_familia'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\GrupoFamiliar::className(), 'targetAttribute' => ['id_familia' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_debitoautomatico' => 'Id Debitoautomatico',
            'id_familia' => 'Id Familia',
            'monto' => 'Monto',
            'resultado' => 'Resultado',
            'correcto' => 'Correcto',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDebitoautomatico()
    {
        return $this->hasOne(\app\models\DebitoAutomatico::className(), ['id' => 'id_debitoautomatico']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFamilia()
    {
        return $this->hasOne(\app\models\GrupoFamiliar::className(), ['id' => 'id_familia']);
    }




}
