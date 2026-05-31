<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ReminderSearch represents the model behind the search form about Reminder
 */
class ReminderSearch extends Reminder
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['description', 'remind_at', 'created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Reminder::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->orderBy(['remind_at' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['remind_at' => SORT_ASC],
                'attributes' => [
                    'remind_at',
                    'created_at',
                    'status',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'remind_at', $this->remind_at]);

        return $dataProvider;
    }
}