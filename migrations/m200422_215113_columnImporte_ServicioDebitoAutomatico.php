<?php

use yii\db\Migration;

/**
 * Class m200422_215113_columnFamilia_DebitoAutomatico
 */
class m200422_215113_columnImporte_ServicioDebitoAutomatico extends Migration
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
        
        $this->addColumn('servicio_debito_automatico', 'importe', $this->float()->notNull());
        //ForeignKey Servicio Ofrecido
        ///$this->addForeignKey('fk_servicio_debito_automatico_familia', 'servicio_debito_automatico', 'id_familia', 'grupo_familiar', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200422_215113_columnFamilia_DebitoAutomatico cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200422_215113_columnFamilia_DebitoAutomatico cannot be reverted.\n";

        return false;
    }
    */
}
