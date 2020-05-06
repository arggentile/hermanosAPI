<?php

use yii\db\Migration;

/**
 * Class m200425_194611_tableDevolucionCBU
 */
class m200428_184613_createTablebonificaciones extends Migration
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
        //create table CategoriaBonificaciones
        $this->createTable('{{%bonificaciones}}', [
            'id' => $this->primaryKey(),            
            'descripcion' => $this->string()->notNull(),
            'valor'=> $this->decimal(12, 2)->notNull(),
            'activa' => $this->binary()->notNull(),
            'automatica' => $this->binary()->notNull(),
            'cantidad_hermanos' => $this->integer()->notNull(),
            
        ], $tableOptions);    
        
        $this->execute('ALTER TABLE bonificaciones MODIFY activa bit(1)');
        $this->execute('ALTER TABLE bonificaciones MODIFY automatica bit(1)');
        
        // Foreign Key
        $this->dropForeignKey('fk_bonificacionSerivioAlumno_bonificacion', 'bonificacion_servicio_alumno');
        $this->addForeignKey('fk_bonificacionSerivioAlumno_bonificacion', 'bonificacion_servicio_alumno', 'id_bonificacion', 'bonificaciones', 'id');
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
