<?php

namespace ZakharovAndrew\user\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use ZakharovAndrew\user\models\UserSettingsConfig;

/**
 * UserSettingsConfigSearch represents the model behind the search form of `ZakharovAndrew\user\models\UserSettingsConfig`.
 */
class UserSettingsConfigSearch extends UserSettingsConfig
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'access_level'], 'integer'],
            [['title', 'code', 'values'], 'safe'],
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
        $query = UserSettingsConfig::find();

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
            'type' => $this->type,
            'access_level' => $this->access_level,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'values', $this->values]);

        return $dataProvider;
    }
}
