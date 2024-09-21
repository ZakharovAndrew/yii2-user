<?php

namespace ZakharovAndrew\user\components;

use yii\base\Widget;
use ZakharovAndrew\user\models\User;

class BirthdayWidget extends Widget
{
    public $useAvatars = false;
    public $headerMessage = 'Today’s birthdays:';
    public $noBirthdaysMessage = 'Today, no one is celebrating a birthday.';
    
    public function run()
    {                
        $today = date('m-d'); // Current date in month-day format
        $users = User::find()
            ->where(['DATE_FORMAT(birthday, "%m-%d")' => $today])
            ->all();

        if (!empty($users)) {
            return $this->render('birthdayWidget', [
                'headerMessage' => $this->headerMessage,
                'useAvatars' => $this->useAvatars,
                'users' => $users
            ]);
        }

        return $this->noBirthdaysMessage;
    }
}