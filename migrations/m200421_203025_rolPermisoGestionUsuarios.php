<?php

use yii\db\Migration;

/**
 * Class m200421_203025_rolPermisoGestionUsuarios
 */
class m200421_203025_rolPermisoGestionUsuarios extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        
        $rolAdministrador = $auth->getRole('administrador');
        
        $rolGestorUsuarios = $auth->createPermission('gestorUsuarios');
        $auth->add($rolGestorUsuarios);
        
        
        $permisoGestionUsuarios = $auth->createPermission('gestionUsuarios');
        $auth->add($permisoGestionUsuarios);
        
        $auth->addChild($rolAdministrador,$permisoGestionUsuarios);
        $auth->addChild($rolGestorUsuarios,$permisoGestionUsuarios);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200421_203025_rolPermisoGestionUsuarios cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200421_203025_rolPermisoGestionUsuarios cannot be reverted.\n";

        return false;
    }
    */
}
