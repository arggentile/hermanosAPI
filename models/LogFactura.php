<?php

namespace app\models;

use Yii;
use \app\models\base\LogFactura as BaseLogFactura;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "log_factura".
 */
class LogFactura extends BaseLogFactura
{

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                 'bedezign\yii2\audit\AuditTrailBehavior'
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
