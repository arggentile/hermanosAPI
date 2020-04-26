<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ServicioDivisionEscolar;

/**
 * ServicioDivisionEscolarSearch represents the model behind the search form of `app\models\ServicioDivisionEscolar`.
 */
class ServicioDivisionEscolarSearch extends ServicioDivisionEscolar
{
    public $establecimiento;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['establecimiento', 'safe'],
            [['id', 'id_servicio', 'id_divisionescolar'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
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
    public function search($params)
    {
        $query = ServicioDivisionEscolar::find();
        $query->joinWith(['divisionescolar d']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
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
            'id_servicio' => $this->id_servicio,
            'id_divisionescolar' => $this->id_divisionescolar,
        ]);

        $query->andFilterWhere([            
            'd.id_establecimiento' => $this->establecimiento,
        ]);
        
        return $dataProvider;
    }
}
