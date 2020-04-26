<?php

use yii\db\Migration;

/**
 * Class m190906_105351_serviciosOfrecidos
 */
class m190906_105351_serviciosOfrecidos extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        echo "Migración Tablas Servicios, Servicios de Establecimiento.\n";
        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {            
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%estado_servicio}}', [
            'id' =>         $this->primaryKey(),
            'descripcion'=> $this->string()->notNull(),
        ], $tableOptions);    
       
        $this->createTable('{{%categoria_servicio_ofrecido}}', [
            'id' =>     $this->primaryKey(),
            'descripcion' => $this->string(50)->notNull()                        
        ], $tableOptions);        
       
        $this->createTable('{{%servicio_ofrecido}}', [
            'id' =>     $this->primaryKey(),
            'id_categoriaservicio' => $this->integer()->notNull(),
            'nombre' => $this->string(100)->notNull(),
            'descripcion'=>  $this->string(),
            'importe'=>$this->decimal(8,2)->notNull(),
            'importe_hijoprofesor'=>$this->decimal(8,2)->notNull(),
            'fecha_inicio' => $this->date(),
            'fecha_fin' => $this->date(),
            'fecha_vencimiento' => $this->date(),
            'devengamiento_automatico'=>$this->binary(1)->notNull(),
            'activo' => $this->binary(1)->notNull(),
            
        ], $tableOptions);
        
        $this->execute('ALTER TABLE servicio_ofrecido MODIFY devengamiento_automatico bit(1)');  
        $this->execute('ALTER TABLE servicio_ofrecido MODIFY activo bit(1)');  
        
        $this->addForeignKey('fk_serviciofrecido_categoriaservicio', 'servicio_ofrecido', 'id_categoriaservicio', 'categoria_servicio_ofrecido', 'id');
        //$this->addForeignKey('fk_serviciofrecido_estadoservicio', 'servicio_ofrecido', 'id_estado', 'estado_servicio', 'id');
               
        $this->createTable('{{%servicio_divisionescolar}}', [
            'id' =>         $this->primaryKey(),
            'id_servicio'=> $this->integer()->notNull(),
            'id_divisionescolar' => $this->integer()->notNull(),          
        ], $tableOptions);
        
        $this->addForeignKey('fk_servicioDivisionEscolar_servio', 'servicio_divisionescolar', 'id_servicio', 'servicio_ofrecido', 'id');
        $this->addForeignKey('fk_servicioDivisionEscolar_divisionescolar', 'servicio_divisionescolar', 'id_divisionescolar', 'division_escolar', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190906_105351_serviciosOfrecidos cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190906_105351_serviciosOfrecidos cannot be reverted.\n";

        return false;
    }
    */
}
