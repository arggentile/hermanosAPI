<?php

use yii\db\Migration;

/**
 * Class m200329_211737_permisosAlumno
 */
class m200329_211737_permisosAlumno extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        
        $perListarAlumno = $auth->createPermission('listarAlumnos');
        $perEliminarAlumno = $auth->createPermission('eliminarAlumno');
        $perCargarAlumno = $auth->createPermission('cargarAlumno');
        $perVisualizarAlumno = $auth->createPermission('visualizarAlumno');
        $perActivarAlumno = $auth->createPermission('activarAlumno');
        $perInactivarAlumno = $auth->createPermission('inactivarAlumno');
        $perExportarAlumno = $auth->createPermission('exportarAlumno');
        $perGestionarBonificacion = $auth->createPermission('gestionarBonificacionAlumno');
        
        $auth->add($perListarAlumno);
        $auth->add($perEliminarAlumno);
        $auth->add($perCargarAlumno);
        $auth->add($perVisualizarAlumno);
        $auth->add($perActivarAlumno);
        $auth->add($perInactivarAlumno);
        $auth->add($perExportarAlumno);
        $auth->add($perGestionarBonificacion);

        
        $perAdministrarAlumnos = $auth->createPermission('administrarAlumnos');        
        $auth->add($perAdministrarAlumnos);
        
        $auth->addChild($perAdministrarAlumnos, $perListarAlumno);
        $auth->addChild($perAdministrarAlumnos, $perEliminarAlumno);
        $auth->addChild($perAdministrarAlumnos, $perCargarAlumno);
        $auth->addChild($perAdministrarAlumnos, $perVisualizarAlumno);
        $auth->addChild($perAdministrarAlumnos, $perActivarAlumno);
        $auth->addChild($perAdministrarAlumnos, $perInactivarAlumno);        
        $auth->addChild($perAdministrarAlumnos, $perExportarAlumno);
        $auth->addChild($perAdministrarAlumnos, $perGestionarBonificacion);
        
        $rolAdministrador = $auth->getRole('administrador');   
        
        $auth->addChild($rolAdministrador, $perAdministrarAlumnos);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200329_211737_permisosAlumno cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200329_211737_permisosAlumno cannot be reverted.\n";

        return false;
    }
    */
}
