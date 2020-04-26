<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Responsable;

/**
 * ResponsableSearch represents the model behind the search form about `app\models\Responsable`.
 */
class ResponsableSearch extends Responsable
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_grupofamiliar', 'id_persona', 'tipo_responsable'], 'integer'],
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
        $query = Responsable::find();
        $query->alias("r");       
        $query->joinWith(['miPersona p']);
        
        var_dump($query->createCommand()->getRawSql());exit;
        
        
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'id_grupofamiliar' => $this->id_grupofamiliar,
            'id_persona' => $this->id_persona,
            'tipo_responsable' => $this->tipo_responsable,
        ]);
        
        $query->andFilterWhere(['like', 'f.apellidos', $this->familia]);        
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

        return $dataProvider;
    }
}
