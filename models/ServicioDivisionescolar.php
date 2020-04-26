<?php

namespace app\models;

use Yii;
use \app\models\base\ServicioDivisionescolar as BaseServicioDivisionescolar;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "servicio_divisionescolar".
 */
class ServicioDivisionescolar extends BaseServicioDivisionescolar
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
