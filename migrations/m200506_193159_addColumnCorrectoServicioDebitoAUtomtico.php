<?php

use yii\db\Migration;

/**
 * Class m200505_193159_columnIniciales_Estalecimiento
 */
class m200506_193159_addColumnCorrectoServicioDebitoAUtomtico extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {            
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->addColumn('servicio_debito_automatico', 'correcto', $this->binary());
        $this->execute('ALTER TABLE servicio_debito_automatico MODIFY correcto bit(1) DEFAULT 0');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200505_193159_columnIniciales_Estalecimiento cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200505_193159_columnIniciales_Estalecimiento cannot be reverted.\n";

        return false;
    }
    */
}
