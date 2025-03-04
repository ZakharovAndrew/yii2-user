<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\models\Notification;

class UserNotificationSetting extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_notification_settings';
    }

    public function rules()
    {
        return [
            [['user_id', 'notification_id'], 'required'],
            [['user_id', 'notification_id'], 'integer'],
            [['send_email', 'send_telegram', 'send_push'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'notification_id' => 'Notification ID',
            'send_email' => 'Send Email',
            'send_telegram' => 'Send Telegram',
            'send_push' => 'Send Push',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getNotification()
    {
        return $this->hasOne(Notification::class, ['id' => 'notification_id']);
    }
}