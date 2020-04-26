<?php

use yii\db\Migration;

/**
 * Class m200331_213331_servicioAlumno
 */
class m200331_213350_changeFkServicioAlumno_ServioOfrecido extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
echo "Migracion Tablas Servicios Alumno.\n";
        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {            
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->dropForeignKey ('fk_servicioAlumno_servio', 'servicio_alumno');
        $this->addForeignKey('fk_servicioAlumno_servio', 'servicio_alumno', 'id_servicio', 'servicio_ofrecido', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200331_213331_servicioAlumno cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200331_213331_servicioAlumno cannot be reverted.\n";

        return false;
    }
    */
}
