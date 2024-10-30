<?php

namespace ZakharovAndrew\user\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserSearch represents the model behind the search form of `ZakharovAndrew\user\models\User`.
 */
class UserSearch extends User
{
    
    public $roles;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'sex', 'roles'], 'integer'],
            [['username', 'auth_key', 'password', 'password_reset_token', 'email', 'name', 'avatar', 'city', 'birthday', 'created_at', 'updated_at'], 'safe'],
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
        $query = User::find();
        
        if (!empty($params['UserSearch']['roles'])) {
            $query->innerJoin('user_roles', 'user_roles.user_id = users.id AND user_roles.role_id='.(int)$params['UserSearch']['roles']);
        }

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
            //'birthday' => $this->birthday,
            'status' => $this->status,
            'sex' => $this->sex,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            //'roles' => $this->roles,
        ]);

        if (isset($this->birthday)) {
            $day = explode('.',$this->birthday);
            
            // day
            if (count($day) > 0) {
                $query->andFilterWhere([
                    'DAY(birthday)' => $day[0],  
                ]);
            }
            
            // month
            if (count($day) > 1) {
                $query->andFilterWhere([
                    'MONTH(birthday)' => $day[1],  
                ]);
            }
            
            // year
            if (count($day) >2) {
                $query->andFilterWhere([
                    'YEAR(birthday)' => $day[2],
                ]);
            }
        }
        
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'avatar', $this->avatar])
            ->andFilterWhere(['like', 'city', $this->city]);

        return $dataProvider;
    }
}
