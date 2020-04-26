<?php

use yii\db\Migration;

/**
 * Class m200418_130053_permisosConvenioPago
 */
class m200418_130053_permisosConvenioPago extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        
        $rolAdministrador = $auth->getRole('administrador');
        $permisoGestionarCP = $auth->getPermission('gestionarConvenioPago');
        
        $listarCP = $auth->createPermission('listarConveioPago');
        $eliminarCP = $auth->createPermission('eliminarConvenioPago');
        $altaCP = $auth->createPermission('cargarConvenioPago');
        $editarCP = $auth->createPermission('editarConvenioPago');
        $visualizarCP = $auth->createPermission('visualizarConvenioPago');
        
        $auth->add($listarCP);
        $auth->add($eliminarCP);
        $auth->add($altaCP);
        $auth->add($editarCP);
        $auth->add($visualizarCP);
        
        $auth->addChild($permisoGestionarCP,$listarCP);
        $auth->addChild($permisoGestionarCP,$eliminarCP);
        $auth->addChild($permisoGestionarCP,$altaCP);
        $auth->addChild($permisoGestionarCP,$editarCP);
        $auth->addChild($permisoGestionarCP,$visualizarCP);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200418_130053_permisosConvenioPago cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200418_130053_permisosConvenioPago cannot be reverted.\n";

        return false;
    }
    */
}
