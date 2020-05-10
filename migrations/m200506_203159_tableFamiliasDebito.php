<?php

use yii\db\Migration;

/**
 * Class m200505_193159_columnIniciales_Estalecimiento
 */
class m200506_203159_tableFamiliasDebito extends Migration
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
        //create table CategoriaBonificaciones
        $this->createTable('{{%debito_automatico_registro}}', [
            'id' => $this->primaryKey(),            
            'id_debitoautomatico' => $this->integer()->notNull(),
            'id_familia'=> $this->integer()->notNull(),
            'monto' => $this->decimal(12,2)->notNull(),
            'resultado' => $this->string(),
            'correcto' => $this->binary()
        ], $tableOptions);   
        
        $this->execute('ALTER TABLE debito_automatico_registro MODIFY correcto bit(1) DEFAULT 0');
        
        $this->addForeignKey('fk_debautomatico_familia', 'debito_automatico_registro', 'id_familia', 'grupo_familiar', 'id');
        $this->addForeignKey('fk_debautomatico_da', 'debito_automatico_registro', 'id_debitoautomatico', 'debito_automatico', 'id');
        //$this->addForeignKey('fk_debautomatico_familia', 'debito_automatico_registro', 'id_familia', 'grupo_familiar', 'id');
        
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
