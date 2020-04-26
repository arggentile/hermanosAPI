<?php

use yii\db\Migration;

/**
 * Class m200403_123017_permisosConveniosPago
 */
class m200403_123017_permisosConveniosPago extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        
        $rolAdministrador = $auth->getRole('administrador');
        
        $permiso = $auth->createPermission('gestionarConvenioPago');
        $auth->add($permiso);
        $auth->addChild($rolAdministrador,$permiso);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200403_123017_permisosConveniosPago cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200403_123017_permisosConveniosPago cannot be reverted.\n";

        return false;
    }
    */
}
