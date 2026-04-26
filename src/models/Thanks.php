<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;
use ZakharovAndrew\user\Module;

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
            [['text'], 'required'],
            [['created_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Module::t('User'),
            'author_id' =>  Module::t('Author'),
            'text' => Module::t('Text'),
            'created_at' => Module::t('Creation date'),
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
    
    public function getCreatedAt()
    {
        $timestamp = strtotime($this->created_at);
        $date = Yii::$app->formatter->asDate($timestamp, 'php:d.m.Y');
        $time = Yii::$app->formatter->asTime($timestamp, 'php:H:i:s');
        
        if ($date == Yii::$app->formatter->asDate(time(), 'php:d.m.Y')) {
            $date = Module::t('Today');
        }
        
        return $date . ' ' . $time;
    }
    
}
