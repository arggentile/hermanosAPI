<?php

use yii\db\Migration;

/**
 * Class m190906_110536_descuentosServicioAlumno
 */
class m190915_105036_addColumnEgresado_fechaEgreso_alumno extends Migration
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
        
        $this->addColumn('alumno', 'egresado', $this->binary());
       
        
        $this->execute('ALTER TABLE alumno MODIFY egresado bit(1)');
        
        
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190915_105036_addColumnEgresado_fechaEgreso_alumno cannot be reverted.\n";

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
