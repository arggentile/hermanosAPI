<?php

use yii\db\Migration;

/**
 * Class m200502_205905_addColumnsTiket
 */
class m200502_205905_addColumnsTiket extends Migration
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
        
        $this->addColumn('tiket', 'con_servicios', $this->binary()->notNull());
        $this->addColumn('tiket', 'id_factura', $this->integer());
        
        $this->execute('ALTER TABLE tiket MODIFY con_servicios bit(1)');
        //ForeignKey Servicio Ofrecido
        $this->addForeignKey('fk_tiket_factura', 'tiket', 'id_factura', 'factura', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200502_205905_addColumnsTiket cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200502_205905_addColumnsTiket cannot be reverted.\n";

        return false;
    }
    */
}
