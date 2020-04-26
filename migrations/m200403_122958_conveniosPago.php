<?php

use yii\db\Migration;

/**
 * Class m200403_122958_conveniosPago
 */
class m200403_122958_conveniosPago extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        echo "Aplicando migracion Convenios Pago.\n";

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {            
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        //create table BonificacionesFamiliares
        $this->createTable('{{%convenio_pago}}', [
            'id' => $this->primaryKey(),
            'nombre'=>$this->string()->notNull(),
            'fecha_alta'=>$this->date()->notNull(),
            'id_familia'=>$this->integer()->notNull(),
            'saldo_pagar'=>$this->decimal(10,2)->notNull(),
            'deb_automatico'=>$this->string(1)->notNull(),
            'descripcion'=>$this->string(),
            'con_servicios'=>$this->string(1)->notNull(),            
        ], $tableOptions);
        
        //create table BonificacionesFamiliares
        $this->createTable('{{%servicio_convenio_pago}}', [
            'id' => $this->primaryKey(),
            'id_conveniopago' => $this->integer()->notNull(),
            'id_servicio'=> $this->integer()->notNull(),            
        ], $tableOptions);
        
        //create table BonificacionesFamiliares
        $this->createTable('{{%cuota_convenio_pago}}', [
            'id' => $this->primaryKey(),
            'id_conveniopago' => $this->integer()->notNull(),
            'fecha_establecida'=> $this->date()->notNull(),
            'nro_cuota'=>$this->integer(2)->notNull(),
            'monto'=> $this->decimal(10,2)->notNull(),
            'estado'=>$this->string(),
            'id_tiket'=>$this->integer(),
            'importe_abonado' => $this->decimal(8,2)->notNull()
        ], $tableOptions);

        $this->addForeignKey('fk_conveniopago_familia', 'convenio_pago', 'id_familia', 'grupo_familiar', 'id');
        
        $this->addForeignKey('fk_servicioCP_conveniopago', 'servicio_convenio_pago', 'id_conveniopago', 'convenio_pago', 'id');
        $this->addForeignKey('fk_servicioCP_servicio', 'servicio_convenio_pago', 'id_servicio', 'servicio_alumno', 'id');        

        $this->addForeignKey('fk_cuotaCP_convenio', 'cuota_convenio_pago', 'id_conveniopago', 'convenio_pago', 'id'); 


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200403_122958_conveniosPago cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200403_122958_conveniosPago cannot be reverted.\n";

        return false;
    }
    */
}
