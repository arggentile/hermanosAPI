<?php

use yii\db\Migration;

/**
 * Class m200425_194611_tableDevolucionCBU
 */
class m200425_194611_tableDevolucionCBU extends Migration
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
        $this->createTable('{{%resultado_cbu_patagonia}}', [
            'id' => $this->primaryKey(),
            'codigo' => $this->string(5)->notNull(), 
            'descripcion' => $this->string()->notNull(),
        ], $tableOptions);

        $this->execute("
INSERT INTO resultado_cbu_patagonia (codigo, descripcion) VALUES
        ('R02','Cuenta cerrada'),
('R03','Cuenta inexistente'),
('R04','Número de cuenta inválido'),
('R08','Orden de no pagar'),
('R10','Falta de fondos'),
('R14','Identificación del cliente en la empresa errónea'),
('R15','Baja del servicio'),
('R17','Error de formato'),
                ('R19','Importe erróneo'),
                ('R20','Moneda distinta a la de la cuenta de débito'),
('R23','Sucursal no habilitada'),
('R24','Transacción duplicada'),
('R25','Error en registro adicional'),
('R26','Error por campo mandatario'),
('R28','Rechazo primer vencimiento'),
('R29','Reversión ya efectuada'),
('R79','Error en campo 7 Registro Individual (Referencia Unívoca del Débito)'),
('R80','Error en campo 3 Registro Adicional (1er motivo de rechazo)'),
('R86','Identificación de la empresa errónea'),
('R91','Código banco incompatible con moneda de TRX'),
('R93','Día no laborable'),
('R95','Reversión de entidad receptora presentada fuera de término'),
('R13','Entidad destino inexistente'),
('R18','Fecha de compensación errónea'),
('R27','Error en contador de registro'),
('R31','Vuelta atrás de Cámara (Unwinding)'),
('R75','Fecha inválida'),
('R76','Error en campo 11 Cabecera de Lote (Código de Origen)'),
('R77','Error en campo 4 Registro Individual (Dígito Verificador 1er bloque de la CBU)'),
('R78','Error en campo 5 Registro Individual (Cuenta a Debitar / Acreditar)'),
('R87','Error en campo 9 Registro Individual 1er byte (Información Adicional a la TRX)'),
('R88','Error en campo 2 Registro Individual (Código de la Transacción)'),
('R89','Errores transacciones no monetarias');
");

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
