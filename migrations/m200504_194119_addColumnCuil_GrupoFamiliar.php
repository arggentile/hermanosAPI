<?php

use yii\db\Migration;

/**
 * Class m200504_194119_addColumnCuil_GrupoFamiliar
 */
class m200504_194119_addColumnCuil_GrupoFamiliar extends Migration
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
        
        $this->addColumn('grupo_familiar', 'cuil_afip_pago', $this->string(11));
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200504_194119_addColumnCuil_GrupoFamiliar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200504_194119_addColumnCuil_GrupoFamiliar cannot be reverted.\n";

        return false;
    }
    */
}
