<?php

use yii\db\Migration;

/**
 * Class m200411_202801_addColumnCuentaPagadora_tableTiket
 */
class m200411_202801_addColumnCuentaPagadora_tableTiket extends Migration
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
        
        $this->addColumn('tiket', 'id_cuentapagadora', $this->integer()->notNull());
        //ForeignKey Servicio Ofrecido
        $this->addForeignKey('fk_tiket_cuentapagadora', 'tiket', 'id_cuentapagadora', 'cuentas', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200411_202801_addColumnCuentaPagadora_tableTiket cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200411_202801_addColumnCuentaPagadora_tableTiket cannot be reverted.\n";

        return false;
    }
    */
}
