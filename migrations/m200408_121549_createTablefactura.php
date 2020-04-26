<?php

use yii\db\Migration;

/**
 * Class m200408_121549_createTablefactura
 */
class m200408_121549_createTablefactura extends Migration
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

        //create table formas_pago
        $this->createTable('{{%factura}}', [
            'id' =>     $this->primaryKey(),
            'fecha_factura' => $this->date()->notNull(),
            'nroFactura'=>  $this->string()->notNull(),   
            'informada'=>$this->string(1)->notNull(),
            'fecha_informada'=>$this->date(),
            'monto'=>$this->decimal(12,2)->notNull(),
            'cae'=>$this->string(),
            'ptoVta'=>$this->string(),
            'id_tiket'=> $this->integer()
        ], $tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200408_121549_createTablefactura cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200408_121549_createTablefactura cannot be reverted.\n";

        return false;
    }
    */
}
