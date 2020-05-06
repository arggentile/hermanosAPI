<?php

use yii\db\Migration;

/**
 * Class m200503_134925_tableLogsFactura
 */
class m200503_134927_tableLogsFacturas extends Migration
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
        $this->createTable('{{%log_factura}}', [
            'id' => $this->primaryKey(),            
            'error' => $this->string()->notNull(),
            'fecha_prueba'=> $this->dateTime()->notNull(),
            'id_factura' => $this->integer()->notNull(),
        ], $tableOptions);    
        
        $this->addForeignKey('fk_logFactura_factura', 'logs_facturas', 'id_factura', 'factura', 'id');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200503_134925_tableLogsFactura cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200503_134925_tableLogsFactura cannot be reverted.\n";

        return false;
    }
    */
}
