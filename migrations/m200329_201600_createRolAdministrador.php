<?php

use yii\db\Migration;

/**
 * Class m200329_211706_permisosEstablecimiento
 */
class m200329_201600_createRolAdministrador extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;        
        
        $rolAdministrador = $auth->createRole('administrador');
        $auth->add($rolAdministrador);
        
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200329_211706_permisosEstablecimiento cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200329_211706_permisosEstablecimiento cannot be reverted.\n";

        return false;
    }
    */
}
