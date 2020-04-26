<?php

use yii\db\Migration;

/**
 * Class m200329_211706_permisosEstablecimiento
 */
class m200329_211600_permisosPrincipales extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;        
              
        $permisoTDocumentos = $auth->createPermission('gestionarDocumentos');
        $permisoTSexos = $auth->createPermission('gestionarSexos');
        $permisoFormaPago = $auth->createPermission('gestionarFormaPago');
        $permisoTResponable = $auth->createPermission('gestionarTipoResponsable');
        $permisoTCategoriaServicio = $auth->createPermission('gestionarCategoriaServicios');
        $permisoClasificacionEgresos = $auth->createPermission('gestionarClasificacionEgresosFondoFijo');   
        $permisoTCategoriaBonificacion = $auth->createPermission('gestionarCategoriaDescuentos');
        
        $auth->add($permisoTDocumentos);
        $auth->add($permisoTSexos);
        $auth->add($permisoFormaPago);
        $auth->add($permisoTResponable);
        $auth->add($permisoTCategoriaServicio);
        $auth->add($permisoTCategoriaBonificacion);
        $auth->add($permisoClasificacionEgresos);
        
        $rolAdministrador = $auth->getRole('administrador');
        $auth->addChild($rolAdministrador, $permisoTDocumentos);
        $auth->addChild($rolAdministrador, $permisoTSexos);
        $auth->addChild($rolAdministrador, $permisoFormaPago);
        $auth->addChild($rolAdministrador,$permisoTResponable);
        $auth->addChild($rolAdministrador,$permisoTCategoriaServicio);
        $auth->addChild($rolAdministrador,$permisoTCategoriaBonificacion);
        $auth->addChild($rolAdministrador,$permisoClasificacionEgresos);

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
