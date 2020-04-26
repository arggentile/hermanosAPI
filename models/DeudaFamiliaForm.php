<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class DeudaFamiliaForm extends Model
{
    public $id_familia;
    public $tipo_servicio; //1:  ; 2:
    public $apellido_familia;
    public $folio_familia;
    public $id_servicio;  //si tipo de servicio es 1;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['id_familia', 'tipo_servicio', 'apellido_familia', 'folio_familia','id_servicio'], 'safe'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'id_familia' => 'Familia',
            'tipo_servicio' => 'Tipo Servicio',
            'apellido_familia' => 'Apellido Familia',
            'folio_familia' => 'Folio Familia',
            'id_servicio' => 'Servicio',            
        ];
    }
    
}
