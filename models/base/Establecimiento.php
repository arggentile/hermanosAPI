<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "establecimiento".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $fecha_apertura
 * @property string $calle
 * @property string $telefono
 * @property string $celular
 * @property string $mail
 * @property string $nivel_educativo
 * @property string $iniciales
 *
 * @property \app\models\DivisionEscolar[] $divisionEscolars
 * @property string $aliasModel
 */
abstract class Establecimiento extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'establecimiento';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'fecha_apertura', 'calle', 'telefono', 'mail', 'nivel_educativo'], 'required'],
            [['fecha_apertura'], 'safe'],
            [['nombre', 'iniciales'], 'string', 'max' => 50],
            [['calle', 'mail'], 'string', 'max' => 255],
            [['telefono', 'celular'], 'string', 'max' => 30],
            [['nivel_educativo'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'fecha_apertura' => 'Fecha Apertura',
            'calle' => 'Calle',
            'telefono' => 'Telefono',
            'celular' => 'Celular',
            'mail' => 'Mail',
            'nivel_educativo' => 'Nivel Educativo',
            'iniciales' => 'Iniciales',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDivisionEscolars()
    {
        return $this->hasMany(\app\models\DivisionEscolar::className(), ['id_establecimiento' => 'id']);
    }




}
