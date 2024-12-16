<?php

namespace ZakharovAndrew\user\components;

use yii\base\Widget;
use ZakharovAndrew\user\models\User;

class BirthdayWidget extends Widget
{
    public $useAvatars = false;
    public $headerMessage = 'Today’s birthdays:';
    public $noBirthdaysMessage = 'Today, no one is celebrating a birthday.';
    public $weekendMessage = 'You might have missed the following birthdays over the weekend:';
    
    public function run()
    {                
        $today = date('m-d'); // Current date in month-day format
        $usersToday = User::find()
            ->where(['DATE_FORMAT(birthday, "%m-%d")' => $today])
            ->all();
        
        // Check if today is Monday
        $isMonday = (date('N') == 1); // 1 means Monday

        // Initialize array for weekend birthdays
        $usersWeekend = [];
        if ($isMonday) {
            // Calculate the dates for the previous Saturday and Sunday
            $saturday = date('m-d', strtotime('last Saturday')); // Get last Saturday's date
            $sunday = date('m-d', strtotime('last Sunday')); // Get last Sunday's date
            //
            // If today is Monday, check for birthdays over the weekend (Saturday and Sunday)
            $usersWeekend = User::find()
                ->where(['DATE_FORMAT(birthday, "%m-%d")' => [$saturday, $sunday]]) // Replace with correct weekend dates
                ->all();
        }


        if (!empty($usersToday) || !empty($usersWeekend)) {
            return $this->render('birthdayWidget', [
                'headerMessage' => $this->headerMessage,
                'useAvatars' => $this->useAvatars,
                'usersToday' => $usersToday,
                'isMonday' => $isMonday,
                'usersWeekend' => $usersWeekend,
                'weekendMessage' => $this->weekendMessage,
            ]);
        }

        return $this->noBirthdaysMessage;
    }
}