<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BonificacionAlumno as BonificacionAlumnoModel;

/**
 * BonificacionAlumno represents the model behind the search form about `app\models\BonificacionAlumno`.
 */
class BonificacionAlumnoSearch extends BonificacionAlumnoModel
{
    public $apellido_alumno;
    public $nombre_alumno;
    public $documento_alumno;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_bonificacion', 'id_alumno'], 'integer'],
            [['apellido_alumno','nombre_alumno','documento_alumno'],'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $persona)
    {
        $session = Yii::$app->session;
        $session->remove('alumnosconbonificaciones');        
        
        $query = BonificacionAlumnoModel::find();
        $query->alias("ba");
       
        $query->joinWith(['alumno a', 'alumno.persona p']);
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,   
            'sort'=>[
                'defaultOrder'=>['apellido'=>SORT_ASC, 'nombre'=>SORT_ASC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'id_bonificacion' => $this->id_bonificacion,
            'id_alumno' => $this->id_alumno,
        ]);
        
        $query->andFilterWhere(['like', 'p.apellido', $persona->apellido]);
        $query->andFilterWhere(['like', 'p.nombre', $persona->nombre]);        
        $query->andFilterWhere(['like', 'p.nro_documento', $persona->nro_documento]);
        
        $dataProvider->sort->attributes['apellido'] = [        
            'asc' => ['p.apellido' => SORT_ASC],
            'desc' => ['p.apellido' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['documento'] = [        
            'asc' => ['p.nro_documento' => SORT_ASC],
            'desc' => ['p.nro_documento' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['nombre'] = [        
            'asc' => ['p.nombre' => SORT_ASC],
            'desc' => ['p.nombre' => SORT_DESC],
        ];
        
        $session->set('alumnosconbonificaciones', $query->createCommand()->getRawSql());

        return $dataProvider;
    }
}
