<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Persona;

/**
 * PersonaSearch represents the model behind the search form of `app\models\Persona`.
 */
class PersonaSearch extends Persona
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_sexo', 'id_tipodocumento'], 'integer'],
            [['apellido', 'nombre', 'fecha_nacimiento', 'nro_documento', 'calle', 'nro_calle', 'piso', 'dpto', 'localidad', 'telefono', 'celular', 'mail', 'grupo_sanguineo', 'factor_rh', 'lugar_nacimiento'], 'safe'],
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
        $query = Persona::find();

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
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'id_sexo' => $this->id_sexo,
            'id_tipodocumento' => $this->id_tipodocumento,
        ]);

        $query->andFilterWhere(['like', 'apellido', $this->apellido])
            ->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'nro_documento', $this->nro_documento])
            ->andFilterWhere(['like', 'calle', $this->calle])
            ->andFilterWhere(['like', 'nro_calle', $this->nro_calle])
            ->andFilterWhere(['like', 'piso', $this->piso])
            ->andFilterWhere(['like', 'dpto', $this->dpto])
            ->andFilterWhere(['like', 'localidad', $this->localidad])
            ->andFilterWhere(['like', 'telefono', $this->telefono])
            ->andFilterWhere(['like', 'celular', $this->celular])
            ->andFilterWhere(['like', 'mail', $this->mail])
            ->andFilterWhere(['like', 'grupo_sanguineo', $this->grupo_sanguineo])
            ->andFilterWhere(['like', 'factor_rh', $this->factor_rh])
            ->andFilterWhere(['like', 'lugar_nacimiento', $this->lugar_nacimiento]);

        return $dataProvider;
    }
}
