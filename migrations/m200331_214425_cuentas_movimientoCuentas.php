<?php

use yii\db\Migration;

/**
 * Class m200331_214425_cuentas
 */
class m200331_214425_cuentas_movimientoCuentas extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        echo "Tabla Cuentas Pagadoras.\n";

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {            
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        //create table Establecimiento
        $this->createTable('{{%cuentas}}', [
            'id' => $this->primaryKey(),
            'nombre' => $this->string()->notNull(),
            'fecha_apertura'=>$this->date()->notNull(),
            'saldo_inicial'=>$this->decimal(10,2)->notNull(),
            'saldo_actual'=>$this->decimal(10,2)->notNull(),           
        ], $tableOptions);
        
        //create table Establecimiento
        $this->createTable('{{%tipo_movimiento_cuenta}}', [
            'id' => $this->primaryKey(),
            'descripcion' => $this->string()->notNull(),
        ], $tableOptions);
        
        
        //create table Establecimiento
        $this->createTable('{{%movimiento_cuenta}}', [
            'id' => $this->primaryKey(),
            'id_cuenta' => $this->integer()->notNull(),
            'id_tipo_movimiento'=>$this->integer()->notNull(),
            'detalle_movimiento'=>$this->string()->notNull(),
            'importe'=>$this->decimal(10,2)->notNull(),
            'fecha_realizacion'=>$this->date()->notNull(),
            'comentario'=> $this->string(),
            'id_tipopago'=> $this->integer(), 
            'id_hijo'=> $this->integer()
        ], $tableOptions);
        
        //ForeignKey Tabla Personas        
        $this->addForeignKey('fk_movimientosCuentas_cuenta', 'movimiento_cuenta', 'id_cuenta', 'cuentas', 'id');
        $this->addForeignKey('fk_movimientosCuentas_formaPago', 'movimiento_cuenta', 'id_tipopago', 'forma_pago', 'id'); 
        $this->addForeignKey('fk_movimientosCuentas_tipoMovimiento', 'movimiento_cuenta', 'id_tipo_movimiento', 'tipo_movimiento_cuenta', 'id'); 
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200331_214425_cuentas cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200331_214425_cuentas cannot be reverted.\n";

        return false;
    }
    */
}
