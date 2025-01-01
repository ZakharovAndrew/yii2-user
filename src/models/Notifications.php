<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\Module;
use \yii\helpers\ArrayHelper;

/**
 * Notifications for users
 * *************
 *  
 * @link https://github.com/ZakharovAndrew/yii2-user/
 * @copyright Copyright (c) 2023-2025 Zakharov Andrew
 */
class Notifications extends \yii\base\Model
{
    /**
     * Notification to users who have linked Telegram: Happy New Year!
     */
    public static function sendNewYearTelegramNotifications()
    {
        $users = User::find()->where('telegram_id is not null')->all();
        
        $token = Yii::$app->getModule('user')->telegramToken;
        $tg = new \ZakharovAndrew\user\models\Telegram($token);
        
        $cntSend = 0;
        
        foreach ($users as $user) {
            $tg->sendMessage($user->telegram_id, Module::t('Happy New Year! Wishing you luck and success in all your endeavors!'));
            
            // for limits
            $cntSend++;
            
            // Sending 30 messages per second
            if ($cntSend > 29) {
                sleep(1);
                $cntSend = 0;
            }
        }
    }
}
