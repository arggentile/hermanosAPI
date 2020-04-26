<?php

use yii\db\Migration;

/**
 * Class m200421_203810_rolAdministradorColegio
 */
class m200421_203810_rolAdministradorColegio extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        
        $rolAdministrador = $auth->getRole('administrador'); 
        $permisoGestionUsuarios = $auth->getPermission('gestionUsuarios');
        $auth->removeChild($rolAdministrador,$permisoGestionUsuarios);
        
        $rolSuperAdmin = $auth->createRole('adminAdmin');    
        $auth->add($rolSuperAdmin);
        $auth->addChild($rolSuperAdmin, $rolAdministrador);
        $auth->addChild($rolSuperAdmin,$permisoGestionUsuarios);
        
        
       
        
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200421_203810_rolAdministradorColegio cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200421_203810_rolAdministradorColegio cannot be reverted.\n";

        return false;
    }
    */
}
