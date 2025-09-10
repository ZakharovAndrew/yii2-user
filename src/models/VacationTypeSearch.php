<?php

namespace ZakharovAndrew\user\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * VacationTypeSearch represents the model behind the search form of `ZakharovAndrew\user\models\VacationType`.
 */
class VacationTypeSearch extends VacationType
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'max_days_per_year', 'min_days_per_request', 'max_days_per_request', 'priority'], 'integer'],
            [['name', 'description', 'color'], 'safe'],
            [['is_paid', 'is_active', 'requires_approval'], 'boolean'],
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
        $query = VacationType::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'priority' => SORT_ASC,
                    'name' => SORT_ASC,
                ]
            ],
            'pagination' => [
                'pageSize' => 20,
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
            'is_paid' => $this->is_paid,
            'is_active' => $this->is_active,
            'requires_approval' => $this->requires_approval,
            'max_days_per_year' => $this->max_days_per_year,
            'min_days_per_request' => $this->min_days_per_request,
            'max_days_per_request' => $this->max_days_per_request,
            'priority' => $this->priority,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'color', $this->color]);

        return $dataProvider;
    }
}