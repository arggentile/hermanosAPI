<?php

use yii\db\Migration;

/**
 * Class m200402_211611_permisosDebitoAutomatico
 */
class m200402_211611_permisosDebitoAutomatico extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        
        $rolAdministrador = $auth->getRole('administrador');
        
        $permiso = $auth->createPermission('gestionarDebitoAutomatico');
        $auth->add($permiso);
        $auth->addChild($rolAdministrador,$permiso);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190518_211118_permisoGestionDebitoAutomatico cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190518_211118_permisoGestionDebitoAutomatico cannot be reverted.\n";

        return false;
    }
    */
}
