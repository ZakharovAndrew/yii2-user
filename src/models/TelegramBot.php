<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\models\User;

/**
 * Telegram BOT
 * *************
 *  
 * @link https://github.com/ZakharovAndrew/yii2-user/
 * @copyright Copyright (c) 2025 Zakharov Andrew
 */
class TelegramBot extends \yii\base\Model
{
    private $token;
    
    public $message;
    
    function __construct($token)
    {
        $this->token = $token;
        
        $body = file_get_contents('php://input');
        
        $json = json_decode($body);
        
        $this->message = $json->message;
        
        //logging
    }

}
