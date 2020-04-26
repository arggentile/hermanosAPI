<?php

use yii\db\Migration;

/**
 * Class m190906_105257_inicial
 */
class m190906_105260_inicial extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        echo "Aplicando migración Incial.\n";
        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {            
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        //create table Establecimiento
        $this->createTable('{{%establecimiento}}', [
            'id' => $this->primaryKey(),
            'nombre' => $this->string(50)->notNull(),
            'fecha_apertura' => $this->date()->notNull(),
            'calle'=>$this->string()->notNull(),
            'telefono'=>$this->string(30)->notNull(),
            'celular'=>$this->string(30),
            'mail'=>$this->string()->notNull(),
            'nivel_educativo'=>$this->string(100)->notNull(),
        ], $tableOptions);
        
        
        //create table DivisionEscolar
        $this->createTable('{{%division_escolar}}', [
            'id' => $this->primaryKey(),
            'nombre' => $this->string()->notNull(),            
            'id_establecimiento'=>$this->integer()->notNull(),
            'id_divisionegreso'=>$this->integer(),
            'iniciales'=>$this->string(100),            
        ], $tableOptions);
        
        //ForeignKey Tabla Division Escolar
        $this->addForeignKey('fk_divisionEscolar_establecimiento', 'division_escolar', 'id_establecimiento', 'establecimiento', 'id');
        $this->addForeignKey('fk_division_divisionegreso', 'division_escolar', 'id_divisionegreso', 'division_escolar', 'id');

        /****************************************************/
        //create table grupo_familiar
        $this->createTable('{{%grupo_familiar}}', [
            'id' => $this->primaryKey(),
            'apellidos' => $this->string()->notNull(),
            'descripcion'=> $this->string(),
            'folio' => $this->string(4)->notNull(),
            'id_pago_asociado'=> $this->integer()->notNull(),
            'cbu_cuenta'=> $this->string(22),
            'nro_tarjetacredito'=> $this->string(16),
            'tarjeta_banco'=> $this->string(50),   
            'prestador_tarjeta'=> $this->string(50),            
        ], $tableOptions);

        
        //ForeignKey Tabla GrupoFamiliar        
        $this->addForeignKey('fk_grupoFamiliar_pagoAsociado', 'grupo_familiar', 'id_pago_asociado', 'forma_pago', 'id');
        
        //create table Persona
        $this->createTable('{{%persona}}', [
            'id' => $this->primaryKey(),
            'apellido' => $this->string()->notNull(),
            'nombre'=> $this->string()->notNull(),
            'fecha_nacimiento'=> $this->date()->notNull(),
            'id_sexo'=> $this->integer()->notNull(),
            'id_tipodocumento'=>$this->integer()->notNull(),
            'nro_documento'=>$this->string(25)->notNull(),
            'calle'=> $this->string()->notNull(),
            'nro_calle'=> $this->string(5)->notNull(),
            'piso'=> $this->string(5),
            'dpto'=> $this->string(5),
            'localidad'=>$this->string(),
            'telefono'=> $this->string(25),
            'celular'=>$this->string(25),
            'mail'=>$this->string(),
            'grupo_sanguineo'=>$this->string(10),
            'factor_rh'=>$this->string(10),      
        ], $tableOptions);  
        
        
        //ForeignKey Tabla Personas        
        $this->addForeignKey('fk_persona_sexo', 'persona', 'id_sexo', 'tipo_sexo', 'id');
        $this->addForeignKey('fk_persona_tipoDocumento', 'persona', 'id_tipodocumento', 'tipo_documento', 'id');       
        
        
        
        //create table responsables
	$this->createTable('{{%responsable}}', [
            'id' => $this->primaryKey(),
            'id_grupofamiliar' => $this->integer()->notNull(),
            'id_persona'=> $this->integer()->notNull(),
            'id_tipo_responsable' => $this->integer()->notNull(),
            'cabecera' =>  $this->binary(),
        ], $tableOptions);        
        
        $this->execute('ALTER TABLE responsable MODIFY cabecera bit(1)');
        
         //ForeignKey Tabla Responsable
        $this->addForeignKey('fk_responsable_grupoFamiliar', 'responsable', 'id_grupofamiliar', 'grupo_familiar', 'id');
        $this->addForeignKey('fk_responsable_persona', 'responsable', 'id_persona', 'persona', 'id');
        $this->addForeignKey('fk_responsable_tiporesponsable', 'responsable', 'id_tipo_responsable', 'tipo_responsable', 'id');
       
        
        
        //create table Alumno
        $this->createTable('{{%alumno}}', [
            'id' => $this->primaryKey(),
            'id_persona' => $this->integer()->notNull(),
            'id_grupofamiliar'=> $this->integer()->notNull(),
            'id_divisionescolar'=> $this->integer()->notNull(),
            'fecha_ingreso'=> $this->date(),
            'fecha_egreso'=> $this->date(),
            'nro_legajo'=>$this->string()->notNull(),
            'activo'=>$this->binary()->notNull(),
            'hijo_profesor'=>$this->binary(),
        ], $tableOptions);       
        
        
        $this->execute('ALTER TABLE alumno MODIFY activo bit(1)');
        $this->execute('ALTER TABLE alumno MODIFY hijo_profesor bit(1)');
        
        //ForeignKey Tabla Alumno
        $this->addForeignKey('fk_alumno_persona', 'alumno', 'id_persona', 'persona', 'id');
        $this->addForeignKey('fk_alumno_grupoFamiliarpersona', 'alumno', 'id_grupoFamiliar', 'grupo_familiar', 'id');
        $this->addForeignKey('fk_alumno_divisionEscolar', 'alumno', 'id_divisionEscolar', 'division_escolar', 'id');   

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
