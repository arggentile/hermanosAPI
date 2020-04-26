<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "cuota_convenio_pago".
 *
 * @property integer $id
 * @property integer $id_conveniopago
 * @property string $fecha_establecida
 * @property integer $nro_cuota
 * @property string $monto
 * @property integer $id_tiket
 * @property string $importe_abonado
 * @property integer $id_estado
 *
 * @property \app\models\EstadoServicio $estado
 * @property \app\models\ConvenioPago $conveniopago
 * @property string $aliasModel
 */
abstract class CuotaConvenioPago extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cuota_convenio_pago';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_conveniopago', 'fecha_establecida', 'nro_cuota', 'monto', 'importe_abonado', 'id_estado'], 'required'],
            [['id_conveniopago', 'nro_cuota', 'id_tiket', 'id_estado'], 'integer'],
            [['fecha_establecida'], 'safe'],
            [['monto', 'importe_abonado'], 'number'],
            [['id_estado'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\EstadoServicio::className(), 'targetAttribute' => ['id_estado' => 'id']],
            [['id_conveniopago'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\ConvenioPago::className(), 'targetAttribute' => ['id_conveniopago' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_conveniopago' => 'Id Conveniopago',
            'fecha_establecida' => 'Fecha Establecida',
            'nro_cuota' => 'Nro Cuota',
            'monto' => 'Monto',
            'id_tiket' => 'Id Tiket',
            'importe_abonado' => 'Importe Abonado',
            'id_estado' => 'Id Estado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstado()
    {
        return $this->hasOne(\app\models\EstadoServicio::className(), ['id' => 'id_estado']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConveniopago()
    {
        return $this->hasOne(\app\models\ConvenioPago::className(), ['id' => 'id_conveniopago']);
    }




}
