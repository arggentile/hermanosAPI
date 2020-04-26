<?php

use yii\db\Migration;

/**
 * Class m200329_211731_permisosGrupoFamiliar
 */
class m200329_211731_permisosGrupoFamiliar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        
        $perListarFamilia = $auth->createPermission('listarFamilias');
        $perVisualizarFamilia = $auth->createPermission('visualizarFamilia');
        $perCargarFamilia = $auth->createPermission('cargarFamilia');
        $perEliminarFamilia = $auth->createPermission('eliminarFamilia');
        $perExportarFamilia = $auth->createPermission('exportarFamilia');
        $perGestionResponsable = $auth->createPermission('gestionarResponable');
        
        $auth->add($perListarFamilia);
        $auth->add($perVisualizarFamilia);
        $auth->add($perCargarFamilia);
        $auth->add($perEliminarFamilia);
        $auth->add($perExportarFamilia);
        $auth->add($perGestionResponsable);

        
        $perAdministrarGrupoFamiliar = $auth->createPermission('administrarGrupoFamiliar');        
        $auth->add($perAdministrarGrupoFamiliar);
        
        $auth->addChild($perAdministrarGrupoFamiliar, $perListarFamilia);
        $auth->addChild($perAdministrarGrupoFamiliar, $perVisualizarFamilia);
        $auth->addChild($perAdministrarGrupoFamiliar, $perCargarFamilia);
        $auth->addChild($perAdministrarGrupoFamiliar, $perEliminarFamilia);
        $auth->addChild($perAdministrarGrupoFamiliar, $perExportarFamilia);
        $auth->addChild($perAdministrarGrupoFamiliar, $perGestionResponsable);
        
        $rolAdministrador = $auth->getRole('administrador');   
        
        $auth->addChild($rolAdministrador, $perAdministrarGrupoFamiliar);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200329_211731_permisosGrupoFamiliar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200329_211731_permisosGrupoFamiliar cannot be reverted.\n";

        return false;
    }
    */
}
