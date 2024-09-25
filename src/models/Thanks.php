<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;

class Thanks extends ActiveRecord
{
    public static function tableName()
    {
        return 'thanks';
    }

    public function rules()
    {
        return [
            [['user_id', 'author_id'], 'integer'],
            [['text'], 'string'],
            [['created_at'], 'safe'],
            [['user_id', 'author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id', 'author_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'author_id' => 'Автор',
            'text' => 'Текст',
            'created_at' => 'Дата создания',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }
}
