<?php

namespace ZakharovAndrew\user\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * VacationSearch represents the model behind the search form of `ZakharovAndrew\user\models\Vacation`.
 */
class VacationSearch extends Vacation
{
    public $user_name;
    public $type_name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type_id', 'days_count', 'status'], 'integer'],
            [['start_date', 'end_date', 'comment', 'created_at', 'updated_at', 'user_name', 'type_name'], 'safe'],
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
        $query = Vacation::find()
            ->joinWith(['user', 'type']);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['user_name'] = [
            'asc' => ['user.name' => SORT_ASC],
            'desc' => ['user.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['type_name'] = [
            'asc' => ['vacation_type.name' => SORT_ASC],
            'desc' => ['vacation_type.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type_id' => $this->type_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'days_count' => $this->days_count,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'user.name', $this->user_name])
            ->andFilterWhere(['like', 'vacation_type.name', $this->type_name]);

        return $dataProvider;
    }
}