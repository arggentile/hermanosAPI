<?php

use yii\db\Migration;

/**
 * Class m200425_194611_tableDevolucionCBU
 */
class m200430_184613_alterFKBonificacionAlumno extends Migration
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
       
        
        // Foreign Key
        $this->dropForeignKey('fk_bonificacionAlumno_bonificacion', 'bonificacion_alumno');
        $this->addForeignKey('fk_bonificacionAlumno_bonificacion', 'bonificacion_alumno', 'id_bonificacion', 'bonificaciones', 'id');
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200425_194611_tableDevolucionCBU cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200425_194611_tableDevolucionCBU cannot be reverted.\n";

        return false;
    }
    */
}
