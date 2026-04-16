<?php

namespace ZakharovAndrew\user\components;

use Yii;
use yii\base\Component;
use ZakharovAndrew\user\models\User;

/**
 * Birthday Notifier Component
 * 
 * @package ZakharovAndrew\user\components
 */
class BirthdayNotifier extends Component
{
    /**
     * @var bool Show age in birthday notifications
     */
    public $showAge = true;
    
    /**
     * @var string Age text template (use {age} placeholder)
     */
    public $ageTemplate = "🎂 Возраст: <b>{age}</b> {years}\n";
    
    /**
     * @var string Message template for birthday notification
     */
    public $messageTemplate = "🎂 <b>День рождения!</b> 🎉\n\nУ вашего друга <b>{friend_name}</b> сегодня день рождения!\n\n{age_text}📅 Дата: <b>{birthday_date}</b>\n\n💝 Не забудьте поздравить друга с праздником!\n\n🎁 Пожелайте ему всего самого лучшего!";
    
    /**
     * @var bool Show buttons in message
     */
    public $showButtons = true;
    
    /**
     * @var string Units for age (years, y.o., etc.)
     */
    public $ageUnits = 'лет';
    
    /**
     * @var array Custom age endings for Russian language
     */
    public $ageEndings = ['год', 'года', 'лет'];
    
    /**
     * Send birthday notifications to friends
     * 
     * @return array Result with statistics
     */
    public function sendNotifications()
    {       
        // Get all users who have birthday today AND have telegram_id
        $birthdayUsers = User::find()
            ->where('birthday IS NOT NULL')
            ->andWhere('birthday != "0000-00-00"')
            ->andWhere(['DATE_FORMAT(birthday, "%m-%d")' => date('m-d')])
            ->all();
        
        if (empty($birthdayUsers)) {
            return [
                'success' => true,
                'message' => 'No birthday users today',
                'sent' => 0
            ];
        }
        
        $totalSent = 0;
        $results = [];
        
        foreach ($birthdayUsers as $birthdayUser) {
            // Get friends of birthday user who have telegram_id
            $friends = $birthdayUser->getFriends()
                ->where('u.telegram_id IS NOT NULL')
                ->andWhere(['!=', 'u.status', User::STATUS_DELETED])
                ->all();
            
            if (empty($friends)) {
                $results[$birthdayUser->id] = [
                    'name' => $birthdayUser->getName(),
                    'friends_count' => 0,
                    'sent' => 0
                ];
                continue;
            }
            
            $sentCount = 0;
            
            foreach ($friends as $friend) {
                $message = $this->prepareMessage($birthdayUser, $friend);
                
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            [
                                'text' => '🎂 Поздравить',
                                'url' => Yii::$app->urlManager->createAbsoluteUrl(['/user/birthday-greeting/send', 'id' => $birthdayUser->id])
                            ],
                            [
                                'text' => '👤 Профиль',
                                'url' => Yii::$app->urlManager->createAbsoluteUrl(['/user/user/profile', 'id' => $birthdayUser->id])
                            ]
                        ]
                    ]
                ];
                
                $result = Yii::$app->telegram->sendMessage(
                    $friend->telegram_id,
                    $message,
                    [
                        'parse_mode' => 'HTML',
                        'reply_markup' => json_encode($keyboard)
                    ]
                );
                
                if ($result['success']) {
                    $sentCount++;
                } else {
                    // Log error
                    Yii::error("Failed to send birthday notification to {$friend->telegram_id}: " . ($result['error'] ?? 'Unknown error'), 'birthday');
                }
            }
            
            $totalSent += $sentCount;
            
            $results[$birthdayUser->id] = [
                'name' => $birthdayUser->getName(),
                'friends_count' => count($friends),
                'sent' => $sentCount
            ];
        }
        
        // Log results
        $this->logResults($results, $totalSent);
        
        return [
            'success' => true,
            'birthday_users' => count($birthdayUsers),
            'sent' => $totalSent,
            'results' => $results
        ];
    }
    
    /**
     * Prepare birthday message
     * 
     * @param User $birthdayUser
     * @param User $friend
     * @return string
     */
    private function prepareMessage($birthdayUser, $friend)
    {
        $age = $this->calculateAge($birthdayUser->birthday);
        $birthdayDate = date('d.m', strtotime($birthdayUser->birthday));
        
        $message = "🎂 <b>День рождения!</b> 🎉\n\n";
        $message .= "У вашего друга <b>" . htmlspecialchars($birthdayUser->getName()) . "</b> сегодня день рождения!\n\n";
        
        // Prepare age text based on settings
        $ageText = '';
        if ($this->showAge && $age > 0) {
            $ageText = str_replace(
                ['{age}', '{years}'],
                [$age, $this->getAgeEnding($age)],
                $this->ageTemplate
            );
        }
        $message .= $ageText;
        
        $message .= "📅 Дата: <b>{$birthdayDate}</b>\n\n";
        $message .= "💝 Не забудьте поздравить друга с праздником!\n\n";
        $message .= "🎁 Пожелайте ему всего самого лучшего!";
        
        return $message;
    }
    
    /**
     * Calculate age from birthday
     * 
     * @param string $birthday
     * @return int
     */
    private function calculateAge($birthday)
    {
        if (empty($birthday) || $birthday == '0000-00-00') {
            return 0;
        }
        
        $birthDate = new \DateTime($birthday);
        $today = new \DateTime();
        return $today->diff($birthDate)->y;
    }
    
    /**
     * Get proper age ending for Russian language
     * 
     * @param int $age
     * @return string
     */
    private function getAgeEnding($age)
    {
        $age = abs($age) % 100;
        if ($age > 10 && $age < 20) {
            return $this->ageEndings[2];
        }
        $age %= 10;
        if ($age == 1) {
            return $this->ageEndings[0];
        }
        if ($age >= 2 && $age <= 4) {
            return $this->ageEndings[1];
        }
        return $this->ageEndings[2];
    }
    
    /**
     * Log results
     * 
     * @param array $results
     * @param int $totalSent
     */
    private function logResults($results, $totalSent)
    {
        $logMessage = date('Y-m-d H:i:s') . " - Birthday notifications sent: {$totalSent}\n";
        
        foreach ($results as $userId => $data) {
            $logMessage .= "  • {$data['name']}: {$data['sent']}/{$data['friends_count']} friends notified\n";
        }
        
        $logFile = Yii::getAlias('@runtime/logs/birthday.log');
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        
        Yii::info("Birthday notifications sent: {$totalSent}", 'birthday');
    }
}