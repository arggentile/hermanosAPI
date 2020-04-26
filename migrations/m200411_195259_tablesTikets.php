<?php

use yii\db\Migration;

/**
 * Class m200411_195259_tablesTikets
 */
class m200411_195259_tablesTikets extends Migration
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
        $this->createTable('{{%tiket}}', [
            'id' => $this->primaryKey(),
            'nro_tiket' => $this->string(50),
            'fecha_tiket' => $this->date()->notNull(),
            'id_tipopago' => $this->integer()->notNull(),           
            'importe'=>$this->decimal(10,2)->notNull(),
            'fecha_pago' => $this->date()->notNull(), 
            'detalles'=>$this->string(),
            'id_cliente'=>$this->integer(),                  
        ], $tableOptions);
        
         
        //create table  Servicios Tiket
        $this->createTable('{{%servicios_tiket}}', [
            'id' => $this->primaryKey(),
            'id_tiket'=>$this->integer()->notNull(),
            'id_servicio'=>$this->integer()->notNull(),
            'tiposervicio'=>$this->integer()->notNull(),            
            'monto_abonado'=>$this->decimal(10,2)->notNull(),
        ], $tableOptions);
        
        //ForeignKey Servicio Ofrecido
        $this->addForeignKey('fk_tiket_tipopago', 'tiket', 'id_tipopago', 'forma_pago', 'id');
        
        $this->addForeignKey('fk_serviciosTiket_tiket', 'servicios_tiket', 'id_tiket', 'tiket', 'id');
        //$this->addForeignKey('fk_serviciosTiket_servicioAbogado', 'servicios_tiket', 'id_servicioabogado', 'servicio_abogado', 'id');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200411_195259_tablesTikets cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200411_195259_tablesTikets cannot be reverted.\n";

        return false;
    }
    */
}
