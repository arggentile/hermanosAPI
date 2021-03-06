<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "convenio_pago".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $fecha_alta
 * @property integer $id_familia
 * @property string $saldo_pagar
 * @property string $deb_automatico
 * @property string $descripcion
 * @property string $con_servicios
 *
 * @property \app\models\GrupoFamiliar $familia
 * @property \app\models\CuotaConvenioPago[] $cuotaConvenioPagos
 * @property \app\models\ServicioConvenioPago[] $servicioConvenioPagos
 * @property string $aliasModel
 */
abstract class ConvenioPago extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'convenio_pago';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'fecha_alta', 'id_familia', 'saldo_pagar', 'deb_automatico', 'con_servicios'], 'required'],
            [['fecha_alta'], 'safe'],
            [['id_familia'], 'integer'],
            [['saldo_pagar'], 'number'],
            [['nombre', 'descripcion'], 'string', 'max' => 255],
            [['deb_automatico', 'con_servicios'], 'string', 'max' => 1],
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
            'nombre' => 'Nombre',
            'fecha_alta' => 'Fecha Alta',
            'id_familia' => 'Id Familia',
            'saldo_pagar' => 'Saldo Pagar',
            'deb_automatico' => 'Deb Automatico',
            'descripcion' => 'Descripcion',
            'con_servicios' => 'Con Servicios',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFamilia()
    {
        return $this->hasOne(\app\models\GrupoFamiliar::className(), ['id' => 'id_familia']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCuotaConvenioPagos()
    {
        return $this->hasMany(\app\models\CuotaConvenioPago::className(), ['id_conveniopago' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicioConvenioPagos()
    {
        return $this->hasMany(\app\models\ServicioConvenioPago::className(), ['id_conveniopago' => 'id']);
    }




}
