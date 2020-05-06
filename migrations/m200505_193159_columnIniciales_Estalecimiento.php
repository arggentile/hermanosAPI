<?php

use yii\db\Migration;

/**
 * Class m200505_193159_columnIniciales_Estalecimiento
 */
class m200505_193159_columnIniciales_Estalecimiento extends Migration
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
        
        $this->addColumn('establecimiento', 'iniciales', $this->string(50));

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
