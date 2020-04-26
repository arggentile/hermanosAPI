<?php

use yii\db\Migration;

/**
 * Class m200329_211706_permisosEstablecimiento
 */
class m200329_211706_permisosEstablecimiento extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        
        $perListarEstablecimientos = $auth->createPermission('listarEstablecimientos');        
        $perCargarEstablecimiento = $auth->createPermission('cargarEstablecimiento');
        $perEliminarEstablecimmiento = $auth->createPermission('eliminarEstablecimiento');        
        $perVisualizarEstablecimiento = $auth->createPermission('visualizarEstablecimiento');
        $perGestionServiciosEstablecimiento = $auth->createPermission('gestionarServiciosEstablecimiento');
        $perGestionarDivisionesEscolares = $auth->createPermission('gestionarDivisionesEscolares');
        
        $auth->add($perListarEstablecimientos);
        $auth->add($perCargarEstablecimiento);
        $auth->add($perEliminarEstablecimmiento);        
        $auth->add($perVisualizarEstablecimiento);        
        $auth->add($perGestionServiciosEstablecimiento);        
        $auth->add($perGestionarDivisionesEscolares);
        
        $perAdministrarEstablecimiento = $auth->createPermission('administrarEstablecimientos');        
        $auth->add($perAdministrarEstablecimiento);
        
        $auth->addChild($perAdministrarEstablecimiento, $perListarEstablecimientos);
        $auth->addChild($perAdministrarEstablecimiento, $perCargarEstablecimiento);
        $auth->addChild($perAdministrarEstablecimiento, $perEliminarEstablecimmiento);
        $auth->addChild($perAdministrarEstablecimiento, $perVisualizarEstablecimiento);
        $auth->addChild($perAdministrarEstablecimiento, $perGestionServiciosEstablecimiento);
        $auth->addChild($perAdministrarEstablecimiento, $perGestionarDivisionesEscolares);
        
        $rolAdministrador = $auth->getRole('administrador');   
        
        $auth->addChild($rolAdministrador, $perAdministrarEstablecimiento);
        
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
