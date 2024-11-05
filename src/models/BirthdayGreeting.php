<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;
use ZakharovAndrew\user\Module;

class BirthdayGreeting extends ActiveRecord
{
    public static function tableName()
    {
        return 'birthday_greeting';
    }

    public function rules()
    {
        return [
            [['user_id', 'author_id'], 'required'],
            [['user_id', 'author_id'], 'integer'],
            [['created_at'], 'safe'],
            [['message'], 'string'],
            [['is_read'], 'boolean'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Module::t('User'),
            'author_id' =>  Module::t('Author'),
            'message' => Module::t('Text'),
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
        $date = date('d.m.Y', strtotime($this->created_at));
        $time = date('H:i:s', strtotime($this->created_at));
        $now = date('d.m.Y', strtotime('now'));
        $yesterday = date('d.m.Y', strtotime('yesterday'));
        
        if ($date == $now) {
            $date = Module::t('Today');
        }
        
        if ($date == $yesterday) {
            $date = Module::t('Yesterday');
        }
        
        return $date . ' ' . $time;
    }
    
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne($this->user_id);
 
        if (!$user) {
            return false;
        }
 
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => '@vendor/zakharov-andrew/yii2-user/src/mail/happy-birthday-html'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($user->email)
            ->setSubject(Module::t('Happy Birthday') . '!')
            ->send();
    }
}
