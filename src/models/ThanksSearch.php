<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use ZakharovAndrew\user\models\Thanks;

class ThanksSearch extends Thanks
{
    public function rules()
    {
        return [
            [['id', 'user_id', 'author_id'], 'integer'],
            [['text', 'created_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $id = null)
    {
        $query = Thanks::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $id ?? $this->user_id,
            'author_id' => $this->author_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}
