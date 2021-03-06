<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "responsable".
 *
 * @property integer $id
 * @property integer $id_grupofamiliar
 * @property integer $id_persona
 * @property integer $id_tipo_responsable
 * @property boolean $cabecera
 *
 * @property \app\models\GrupoFamiliar $grupofamiliar
 * @property \app\models\Persona $persona
 * @property \app\models\TipoResponsable $tipoResponsable
 * @property string $aliasModel
 */
abstract class Responsable extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'responsable';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_grupofamiliar', 'id_persona', 'id_tipo_responsable'], 'required'],
            [['id_grupofamiliar', 'id_persona', 'id_tipo_responsable'], 'integer'],
            [['cabecera'], 'boolean'],
            [['id_grupofamiliar'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\GrupoFamiliar::className(), 'targetAttribute' => ['id_grupofamiliar' => 'id']],
            [['id_persona'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Persona::className(), 'targetAttribute' => ['id_persona' => 'id']],
            [['id_tipo_responsable'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\TipoResponsable::className(), 'targetAttribute' => ['id_tipo_responsable' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_grupofamiliar' => 'Id Grupofamiliar',
            'id_persona' => 'Id Persona',
            'id_tipo_responsable' => 'Id Tipo Responsable',
            'cabecera' => 'Cabecera',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupofamiliar()
    {
        return $this->hasOne(\app\models\GrupoFamiliar::className(), ['id' => 'id_grupofamiliar']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersona()
    {
        return $this->hasOne(\app\models\Persona::className(), ['id' => 'id_persona']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoResponsable()
    {
        return $this->hasOne(\app\models\TipoResponsable::className(), ['id' => 'id_tipo_responsable']);
    }




}
