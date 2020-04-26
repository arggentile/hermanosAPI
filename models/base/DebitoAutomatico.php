<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace app\models\base;

use Yii;

/**
 * This is the base-model class for table "debito_automatico".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $banco
 * @property string $tipo_archivo
 * @property string $fecha_creacion
 * @property string $fecha_procesamiento
 * @property string $inicio_periodo
 * @property string $fin_periodo
 * @property string $fecha_debito
 * @property boolean $procesado
 * @property integer $registros_enviados
 * @property integer $registros_correctos
 * @property string $saldo_enviado
 * @property string $saldo_entrante
 *
 * @property \app\models\ServicioDebitoAutomatico[] $servicioDebitoAutomaticos
 * @property string $aliasModel
 */
abstract class DebitoAutomatico extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'debito_automatico';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'banco', 'tipo_archivo', 'fecha_creacion', 'inicio_periodo', 'fin_periodo', 'fecha_debito', 'saldo_enviado'], 'required'],
            [['fecha_creacion', 'fecha_procesamiento', 'inicio_periodo', 'fin_periodo', 'fecha_debito'], 'safe'],
            [['procesado'], 'boolean'],
            [['registros_enviados', 'registros_correctos'], 'integer'],
            [['saldo_enviado', 'saldo_entrante'], 'number'],
            [['nombre', 'banco'], 'string', 'max' => 100],
            [['tipo_archivo'], 'string', 'max' => 50]
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
            'banco' => 'Banco',
            'tipo_archivo' => 'Tipo Archivo',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_procesamiento' => 'Fecha Procesamiento',
            'inicio_periodo' => 'Inicio Periodo',
            'fin_periodo' => 'Fin Periodo',
            'fecha_debito' => 'Fecha Debito',
            'procesado' => 'Procesado',
            'registros_enviados' => 'Registros Enviados',
            'registros_correctos' => 'Registros Correctos',
            'saldo_enviado' => 'Saldo Enviado',
            'saldo_entrante' => 'Saldo Entrante',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicioDebitoAutomaticos()
    {
        return $this->hasMany(\app\models\ServicioDebitoAutomatico::className(), ['id_debitoautomatico' => 'id']);
    }




}
