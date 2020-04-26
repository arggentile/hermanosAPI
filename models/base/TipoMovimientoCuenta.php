<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "tipo_movimiento_cuenta".
 *
 * @property integer $id
 * @property string $descripcion
 *
 * @property \app\models\MovimientoCuenta[] $movimientoCuentas
 * @property string $aliasModel
 */
abstract class TipoMovimientoCuenta extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipo_movimiento_cuenta';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descripcion'], 'required'],
            [['descripcion'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descripcion' => 'Descripcion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMovimientoCuentas()
    {
        return $this->hasMany(\app\models\MovimientoCuenta::className(), ['id_tipo_movimiento' => 'id']);
    }




}