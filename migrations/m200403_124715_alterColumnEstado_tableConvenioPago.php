<?php

use yii\db\Migration;

/**
 * Class m200403_124715_alterColumnEstado_tableConvenioPago
 */
class m200403_124715_alterColumnEstado_tableConvenioPago extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        echo "Aplicando migracion Convenios Pago.\n";

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {            
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->dropColumn('{{%cuota_convenio_pago}}', 'estado');
        $this->addColumn('{{%cuota_convenio_pago}}', 'id_estado', $this->integer()->notNull());
        $this->addForeignKey('fk_cuotaCP', 'cuota_convenio_pago', 'id_estado', 'estado_servicio', 'id'); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200403_124715_alterColumnEstado_tableConvenioPago cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200403_124715_alterColumnEstado_tableConvenioPago cannot be reverted.\n";

        return false;
    }
    */
}
