<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\models\User;

/**
 * Telegram
 * *************
 *  
 * @link https://github.com/ZakharovAndrew/yii2-user/
 * @copyright Copyright (c) 2023-2024 Zakharov Andrew
 */
class Telegram extends \yii\base\Model
{
    private $token;
    
    const BASE_API_URL = 'https://api.telegram.org/bot' ;
    
    function __construct($token) {
        $this->token = $token;
    }

    public function sendMessage($chatID, $message)
    {
        $url = self::BASE_API_URL . $this->token."/sendMessage?chat_id=".$chatID."&text=".urlencode($message);
        
        return file_get_contents($url);
    }
}
