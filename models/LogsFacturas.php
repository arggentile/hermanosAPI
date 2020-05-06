<?php

namespace app\models;

use Yii;
use \app\models\base\LogsFacturas as BaseLogsFacturas;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "logs_facturas".
 */
class LogsFacturas extends BaseLogsFacturas
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                # custom validation rules
            ]
        );
    }
}
