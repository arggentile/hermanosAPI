<?php

use yii\db\Migration;

/**
 * Class m190906_110536_descuentosServicioAlumno
 */
class m190912_105036_addColumn_lugarNacimineto_persona extends Migration
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
        
        $this->addColumn('persona', 'lugar_nacimiento', $this->string());
        
        
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190906_110536_descuentosServicioAlumno cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190906_110536_descuentosServicioAlumno cannot be reverted.\n";

        return false;
    }
    */
}
