<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "logs_facturas".
 *
 * @property integer $id
 * @property string $error
 * @property string $fecha_prueba
 * @property integer $id_factura
 *
 * @property \app\models\Factura $factura
 * @property \app\models\Factura $factura0
 * @property string $aliasModel
 */
abstract class LogsFacturas extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logs_facturas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['error', 'fecha_prueba', 'id_factura'], 'required'],
            [['fecha_prueba'], 'safe'],
            [['id_factura'], 'integer'],
            [['error'], 'string', 'max' => 255],
            [['id_factura'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Factura::className(), 'targetAttribute' => ['id_factura' => 'id']],
            [['id_factura'], 'exist', 'skipOnError' => true, 'targetClass' => \app\models\Factura::className(), 'targetAttribute' => ['id_factura' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'error' => 'Error',
            'fecha_prueba' => 'Fecha Prueba',
            'id_factura' => 'Id Factura',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFactura()
    {
        return $this->hasOne(\app\models\Factura::className(), ['id' => 'id_factura']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFactura0()
    {
        return $this->hasOne(\app\models\Factura::className(), ['id' => 'id_factura']);
    }




}