<?php

use yii\db\Migration;

/**
 * Class m200417_151330_tableHistoriaDivisionAlumno
 */
class m200417_151331_tableHistoriaDivisionAlumno extends Migration
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
        
        //create table Categoria Servicios Ofrecidos
        $this->createTable('{{%historia_egresos_alumno}}', [
            'id' => $this->primaryKey(),
            'id_alumno' => $this->integer()->notNull(), 
            'id_division_actual' => $this->integer()->notNull(), 
            'id_division_egreso' => $this->integer(),  
            'fecha' => $this->date()->notNull()
        ], $tableOptions);
        
         
        
        //ForeignKey Servicio Ofrecido
        $this->addForeignKey('fk_histEgresosAlumno_alumno', 'historia_egresos_alumno', 'id_alumno', 'alumno', 'id');
        $this->addForeignKey('fk_histEgresosAlumno_divisionActual', 'historia_egresos_alumno', 'id_division_actual', 'division_escolar', 'id');
        $this->addForeignKey('fk_histEgresosAlumno_divisionEgreso', 'historia_egresos_alumno', 'id_division_egreso', 'division_escolar', 'id');
        
       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200417_151330_tableHistoriaDivisionAlumno cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200417_151330_tableHistoriaDivisionAlumno cannot be reverted.\n";

        return false;
    }
    */
}
