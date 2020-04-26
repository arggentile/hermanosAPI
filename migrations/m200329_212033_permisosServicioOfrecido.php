<?php

use yii\db\Migration;

/**
 * Class m200329_212033_permisosServicioOfrecido
 */
class m200329_212033_permisosServicioOfrecido extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $auth = Yii::$app->authManager;
        
        $permisoAbmlSO = $auth->createPermission('abmlServicioOfrecido');
        $permisoDevengamientoSO = $auth->createPermission('devengarServicioOfrecido');
        $permisoGestionarServicioOfrecido = $auth->createPermission('gestionarServicios');        
        $auth->add($permisoAbmlSO);
        $auth->add($permisoDevengamientoSO);
        $auth->add($permisoGestionarServicioOfrecido);
        $auth->addChild($permisoGestionarServicioOfrecido, $permisoDevengamientoSO);
        $auth->addChild($permisoGestionarServicioOfrecido, $permisoAbmlSO);
        
        $permisoRemoverServicio = $auth->createPermission('removerServicioAlumno');        
        $auth->add($permisoRemoverServicio);
        $auth->addChild($permisoGestionarServicioOfrecido, $permisoRemoverServicio);
        
        $rolAdministrador = $auth->getRole('administrador');           
        $auth->addChild($rolAdministrador, $permisoGestionarServicioOfrecido);

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200329_212033_permisosServicioOfrecido cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200329_212033_permisosServicioOfrecido cannot be reverted.\n";

        return false;
    }
    */
}
