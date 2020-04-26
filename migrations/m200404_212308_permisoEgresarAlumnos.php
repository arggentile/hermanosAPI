<?php

use yii\db\Migration;

/**
 * Class m200404_212308_permisoEgresarAlumnos
 */
class m200404_212308_permisoEgresarAlumnos extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        
        $rolAdministrador = $auth->getRole('administrador');
        
        $permiso = $auth->createPermission('egresarAlumnos');
        $auth->add($permiso);
        $auth->addChild($rolAdministrador,$permiso);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200404_212308_permisoEgresarAlumnos cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200404_212308_permisoEgresarAlumnos cannot be reverted.\n";

        return false;
    }
    */
}
