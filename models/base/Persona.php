<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "persona".
 *
 * @property integer $id
 * @property string $apellido
 * @property string $nombre
 * @property string $fecha_nacimiento
 * @property integer $id_sexo
 * @property integer $id_tipodocumento
 * @property string $nro_documento
 * @property string $calle
 * @property string $nro_calle
 * @property string $piso
 * @property string $dpto
 * @property string $localidad
 * @property string $telefono
 * @property string $celular
 * @property string $mail
 * @property string $grupo_sanguineo
 * @property string $factor_rh
 * @property string $lugar_nacimiento
 *
 * @property \app\models\Alumno[] $alumnos
 * @property \app\models\TipoSexo $sexo
 * @property \app\models\TipoDocumento $tipodocumento
 * @property \app\models\Responsable[] $responsables
 * @property string $aliasModel
 */
abstract class Persona extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'persona';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['apellido', 'nombre', 'fecha_nacimiento', 'id_sexo', 'id_tipodocumento', 'nro_documento', 'calle', 'nro_calle'], 'required'],
            [['fecha_nacimiento'], 'safe'],
            [['id_sexo', 'id_tipodocumento'], 'integer'],
            [['apellido', 'nombre', 'calle', 'localidad', 'mail', 'lugar_nacimiento'], 'string', 'max' => 255],
            [['nro_documento', 'telefono', 'celular'], 'string', 'max' => 25],
            [['nro_calle', 'piso', 'dpto'], 'string', 'max' => 5],
            [['grupo_sanguineo', 'factor_rh'], 'string', 'max' => 10],
            [['id_sexo'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\TipoSexo::className(), 'targetAttribute' => ['id_sexo' => 'id']],
            [['id_tipodocumento'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\TipoDocumento::className(), 'targetAttribute' => ['id_tipodocumento' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'apellido' => 'Apellido',
            'nombre' => 'Nombre',
            'fecha_nacimiento' => 'Fecha Nacimiento',
            'id_sexo' => 'Id Sexo',
            'id_tipodocumento' => 'Id Tipodocumento',
            'nro_documento' => 'Nro Documento',
            'calle' => 'Calle',
            'nro_calle' => 'Nro Calle',
            'piso' => 'Piso',
            'dpto' => 'Dpto',
            'localidad' => 'Localidad',
            'telefono' => 'Telefono',
            'celular' => 'Celular',
            'mail' => 'Mail',
            'grupo_sanguineo' => 'Grupo Sanguineo',
            'factor_rh' => 'Factor Rh',
            'lugar_nacimiento' => 'Lugar Nacimiento',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlumnos()
    {
        return $this->hasMany(\app\models\Alumno::className(), ['id_persona' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSexo()
    {
        return $this->hasOne(\app\models\TipoSexo::className(), ['id' => 'id_sexo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipodocumento()
    {
        return $this->hasOne(\app\models\TipoDocumento::className(), ['id' => 'id_tipodocumento']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResponsables()
    {
        return $this->hasMany(\app\models\Responsable::className(), ['id_persona' => 'id']);
    }




}
