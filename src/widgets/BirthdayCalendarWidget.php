<?php

namespace ZakharovAndrew\user\widgets;

use Yii;
use yii\base\Widget;
use ZakharovAndrew\user\models\User;

class BirthdayCalendarWidget extends Widget
{
    public $title = 'Birthday Calendar (Next Month)';
    public $view = 'birthday-calendar';
    public $showAge = true;
    public $maxUsersPerDay = 5; // Maximum number of users to show per day

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        // Get birthdays for current week and next month
        $birthdays = $this->getBirthdays();
        
        // Generate calendar data
        $calendar = $this->generateCalendar($birthdays);
        
        return $this->render($this->view, [
            'calendar' => $calendar,
            'title' => $this->title,
            'showAge' => $this->showAge,
            'maxUsersPerDay' => $this->maxUsersPerDay,
        ]);
    }

    /**
     * Get birthdays for current week and next month
     * @return array Array of birthdays grouped by date
     */
    protected function getBirthdays()
    {
        $currentDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 month'));
        
        $users = User::find()
            ->where(['!=', 'status', User::STATUS_DELETED])
            ->andWhere(['IS NOT', 'birthday', null])
            ->all();
        
        $birthdays = [];
        
        foreach ($users as $user) {
            if (!empty($user->birthday)) {
                // Get birthday in current year for comparison
                $birthdayThisYear = date('Y') . substr($user->birthday, 4);
                
                // Check if birthday falls within our date range (current week to next month)
                if ($birthdayThisYear >= $currentDate && $birthdayThisYear <= $endDate) {
                    $birthdays[$birthdayThisYear][] = [
                        'user' => $user,
                        'age' => $this->calculateAge($user->birthday),
                        'birthday' => $user->birthday
                    ];
                }
            }
        }
        
        // Sort by date
        ksort($birthdays);
        
        return $birthdays;
    }

    /**
     * Calculate age based on birthday
     * @param string $birthday Birthday date
     * @return int Age
     */
    protected function calculateAge($birthday)
    {
        $birthDate = new \DateTime($birthday);
        $currentDate = new \DateTime();
        return $currentDate->diff($birthDate)->y;
    }

    /**
     * Generate calendar data structure
     * @param array $birthdays Array of birthdays
     * @return array Calendar data
     */
    protected function generateCalendar($birthdays)
    {
        $calendar = [];
        $currentDate = new \DateTime();
        $endDate = new \DateTime('+1 month');
        
        // Group birthdays by week
        $weekStart = clone $currentDate;
        $weekStart->modify('this week'); // Start from beginning of current week
        
        while ($weekStart <= $endDate) {
            $weekEnd = clone $weekStart;
            $weekEnd->modify('+6 days');
            
            $weekData = [
                'week_number' => $weekStart->format('W'),
                'start_date' => $weekStart->format('Y-m-d'),
                'end_date' => $weekEnd->format('Y-m-d'),
                'days' => []
            ];
            
            // Add days for this week
            $dayDate = clone $weekStart;
            for ($i = 0; $i < 7; $i++) {
                $dateString = $dayDate->format('Y-m-d');
                
                $weekData['days'][] = [
                    'date' => $dateString,
                    'day_name' => $dayDate->format('D'),
                    'day_number' => $dayDate->format('j'),
                    'is_today' => $dateString === date('Y-m-d'),
                    'is_past' => $dateString < date('Y-m-d'),
                    'is_future' => $dateString > date('Y-m-d'),
                    'birthdays' => isset($birthdays[$dateString]) ? $birthdays[$dateString] : []
                ];
                
                $dayDate->modify('+1 day');
            }
            
            $calendar[] = $weekData;
            $weekStart->modify('+1 week');
        }
        
        return $calendar;
    }

    /**
     * Format date for display
     * @param string $date Date string
     * @return string Formatted date
     */
    public function formatDate($date)
    {
        return Yii::$app->formatter->asDate($date, 'medium');
    }
}
