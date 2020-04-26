<?php

use yii\db\Migration;

/**
 * Class m190906_105257_inicial
 */
class m190906_105257_inicialConfig extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        echo "Aplicando migración Incial, tablas para configuraciones y llenado de datos estandrs\n";
        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {            
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }       

        
        /*******************************************************/
        //create table formas_pago
        $this->createTable('{{%forma_pago}}', [
            'id' =>     $this->primaryKey(),
            'nombre' => $this->string(100)->notNull(),
            'siglas'=>  $this->string(30)->notNull()            
        ], $tableOptions);
        
        //create table tipo_sexo
        $this->createTable('{{%tipo_sexo}}', [
            'id' => $this->primaryKey(),
            'nombre' => $this->string(100)->notNull(),
            'siglas'=> $this->string(30)->notNull()            
        ], $tableOptions);
        //create table tipo_documento
        $this->createTable('{{%tipo_documento}}', [
            'id' => $this->primaryKey(),
            'nombre' => $this->string(100)->notNull(),
            'siglas'=> $this->string(30)->notNull()            
        ], $tableOptions);
        
        //create table Tipo Responsable
        $this->createTable('{{%tipo_responsable}}', [
            'id' => $this->primaryKey(),
            'nombre' => $this->string(100)->notNull(),
            'siglas' => $this->string(30),
        ], $tableOptions);       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190906_105257_inicial cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190906_105257_inicial cannot be reverted.\n";

        return false;
    }
    */
}
