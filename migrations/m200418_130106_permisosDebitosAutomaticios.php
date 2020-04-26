<?php

use yii\db\Migration;

/**
 * Class m200418_130106_permisosDebitosAutomaticios
 */
class m200418_130106_permisosDebitosAutomaticios extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        
        $rolAdministrador = $auth->getRole('administrador');
        $permisoGestion = $auth->getPermission('gestionarDebitoAutomatico');
        
        $listarDA = $auth->createPermission('listarDebitoAutomatico');
        $eliminarDA = $auth->createPermission('eliminarDebitoAutomatico');
        $altaDA = $auth->createPermission('altaDebitoAutomatico');
        $procesarDA = $auth->createPermission('procesarDebitoAutomatico');
        $visualizarDA = $auth->createPermission('visualizarDebitoAutomatico');
        
        $auth->add($listarDA);
        $auth->add($eliminarDA);
        $auth->add($altaDA);
        $auth->add($procesarDA);
        $auth->add($visualizarDA);
        
        $auth->addChild($permisoGestion,$listarDA);
        $auth->addChild($permisoGestion,$eliminarDA);
        $auth->addChild($permisoGestion,$altaDA);
        $auth->addChild($permisoGestion,$procesarDA);
        $auth->addChild($permisoGestion,$visualizarDA);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200418_130106_permisosDebitosAutomaticios cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200418_130106_permisosDebitosAutomaticios cannot be reverted.\n";

        return false;
    }
    */
}
