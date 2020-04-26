<?php

use yii\db\Migration;

/**
 * Class m200418_130106_permisosDebitosAutomaticios
 */
class m200422_130106_permisosServicioAlumno extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        
        $rolSuperAdmin = $auth->getRole('administrador');
        $rolAdministrador = $auth->getRole('administrador');
        
        $permisoVerDetalleServicio = $auth->createPermission('verDetalleServicioAlumno');
        $permisoEliminarServicio = $auth->createPermission('eliminarServicioAlumno');
        $permisoEditarServicio = $auth->createPermission('editarServicioAlumno');
        
        $auth->add($permisoVerDetalleServicio);
        $auth->add($permisoEliminarServicio);
        $auth->add($permisoEditarServicio);
        
        $auth->addChild($rolAdministrador,$permisoVerDetalleServicio);
        $auth->addChild($rolAdministrador,$permisoEliminarServicio);
        $auth->addChild($rolAdministrador,$permisoEditarServicio);
        
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
