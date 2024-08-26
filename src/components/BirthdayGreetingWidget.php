<?php

namespace ZakharovAndrew\user\components;

use Yii;
use yii\base\Widget;

class BirthdayGreetingWidget extends Widget
{
    /**
     * @var string Custom greeting message
     */
    public $message = "Happy Birthday, {username}!";

    public function run()
    {
        // Getting the current user
        $currentUser = Yii::$app->user->identity;

        if ($currentUser) {
            // Get the current date in month-day format
            $today = date('m-d');

            // Checking if the user's birthday coincides with today's date
            if (date('m-d', strtotime($currentUser->birthday)) === $today) {
                // Replace {username} and {name} in the message with the username
                return str_replace(
                        ['{username}', '{name}'],
                        [$currentUser->username, $currentUser->name],
                        $this->message
                    );
            }
        }

        // If the user's birthday is not celebrated today
        return '';
    }
}