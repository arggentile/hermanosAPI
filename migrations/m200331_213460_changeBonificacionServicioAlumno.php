<?php

use yii\db\Migration;

/**
 * Class m200331_213455_bonificacionServicioAlumno
 */
class m200331_213460_changeBonificacionServicioAlumno extends Migration
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
        $this->dropForeignKey('fk_bonificacionSerivioAlumno_servicioAlumno', 'bonificacion_servicio_alumno');
        $this->dropForeignKey('fk_bonificacionSerivioAlumno_bonificacion', 'bonificacion_servicio_alumno');
        
        // Foreign Key
        $this->addForeignKey('fk_bonificacionSerivioAlumno_servicioAlumno', 'bonificacion_servicio_alumno', 'id_servicioalumno', 'servicio_alumno', 'id', 'CASCADE');
        $this->addForeignKey('fk_bonificacionSerivioAlumno_bonificacion', 'bonificacion_servicio_alumno', 'id_bonificacion', 'categoria_bonificacion', 'id');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200331_213455_bonificacionServicioAlumno cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200331_213455_bonificacionServicioAlumno cannot be reverted.\n";

        return false;
    }
    */
}
