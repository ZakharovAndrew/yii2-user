<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $email
 * @property int $action (1 - send_code, 2 - verify_code)
 * @property string $ip_address
 * @property string $user_agent
 * @property int $status (1 - success, 2 - failed)
 * @property string|null $error_message
 * @property int $created_at
 */
class VerificationLog extends ActiveRecord
{
    // Actions
    const ACTION_SEND_CODE = 1;
    const ACTION_VERIFY_CODE = 2;
    
    // Statuses
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;
    
    // Limit types
    const LIMIT_TYPE_COOLDOWN = 1;     // Too frequent requests
    const LIMIT_TYPE_MAX_ATTEMPTS = 2; // Too many failed attempts
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%verification_log}}';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'action', 'ip_address', 'status'], 'required'],
            [['user_id', 'action', 'status'], 'integer'],
            [['email'], 'string', 'max' => 150],
            [['ip_address'], 'string', 'max' => 45],
            [['user_agent'], 'string', 'max' => 500],
            [['error_message'], 'string', 'max' => 1000],
            ['action', 'in', 'range' => [self::ACTION_SEND_CODE, self::ACTION_VERIFY_CODE]],
            ['status', 'in', 'range' => [self::STATUS_SUCCESS, self::STATUS_FAILED]],
            ['user_id', 'default', 'value' => null],
            ['created_at', 'safe'],
        ];
    }
    
    /**
     * Get action mapping for convenience
     */
    public static function getActionLabels()
    {
        return [
            self::ACTION_SEND_CODE => 'send_code',
            self::ACTION_VERIFY_CODE => 'verify_code',
        ];
    }
    
    /**
     * Get status mapping for convenience
     */
    public static function getStatusLabels()
    {
        return [
            self::STATUS_SUCCESS => 'success',
            self::STATUS_FAILED => 'failed',
        ];
    }
    
    /**
     * Get action text description
     */
    public function getActionText()
    {
        $labels = self::getActionLabels();
        return $labels[$this->action] ?? 'unknown';
    }
    
    /**
     * Get status text
     */
    public function getStatusText()
    {
        $labels = self::getStatusLabels();
        return $labels[$this->status] ?? 'unknown';
    }
    
    /**
     * Log verification attempt
     */
    public static function log($userId, $email, $action, $status, $errorMessage = null)
    {
        $log = new self();
        $log->user_id = $userId;
        $log->email = $email;
        $log->action = $action;
        $log->ip_address = self::getClientIp();
        $log->user_agent = self::getUserAgent();
        $log->status = $status;
        $log->error_message = $errorMessage;
        $log->created_at = date('Y-m-d H:i:s');
        
        return $log->save(false);
    }
    
    /**
     * Check if there were too many failed attempts recently
     */
    public static function hasTooManyFailedAttempts($email, $maxAttempts = 5, $timeWindow = 3600)
    {
        $timeAgo = time() - $timeWindow;
        
        $count = self::find()
            ->where(['email' => $email])
            ->andWhere(['action' => self::ACTION_VERIFY_CODE])
            ->andWhere(['status' => self::STATUS_FAILED])
            ->andWhere(['>=', 'created_at', $timeAgo])
            ->count();
            
        return $count >= $maxAttempts;
    }
    
    /**
     * Check rate limiting for sending codes
     */
    public static function checkSendRateLimit($email, $cooldownSeconds = 60)
    {       
        $lastAttempt = self::find()
            ->where(['email' => $email])
            ->andWhere(['action' => self::ACTION_SEND_CODE])
            ->andWhere(['status' => self::STATUS_SUCCESS])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
        
        if (!$lastAttempt || !$lastAttempt->created_at) {
            return ['allowed' => true];
        }
    
        $lastAttemptTime = strtotime($lastAttempt->created_at);
        $timeSinceLastAttempt = strtotime('now') - $lastAttemptTime;
        
        if ($timeSinceLastAttempt < $cooldownSeconds) {
            $remainingSeconds = $cooldownSeconds - $timeSinceLastAttempt;
            
            return [
                'allowed' => false,
                'remaining' => $remainingSeconds,
                'last_attempt' => $lastAttemptTime,
                'next_attempt_allowed_at' => $lastAttemptTime + $cooldownSeconds,
                'limit_type' => self::LIMIT_TYPE_COOLDOWN
            ];
        }
        
        return ['allowed' => true];
    }
    
    /**
     * Get statistics by email
     */
    public static function getEmailStats($email, $hours = 24)
    {
        $timeAgo = time() - ($hours * 3600);
        
        $query = self::find()
            ->where(['email' => $email])
            ->andWhere(['>=', 'created_at', $timeAgo]);
            
        return [
            'total_attempts' => (int)$query->count(),
            'send_code_success' => (int)$query->andWhere([
                'action' => self::ACTION_SEND_CODE,
                'status' => self::STATUS_SUCCESS
            ])->count(),
            'send_code_failed' => (int)$query->andWhere([
                'action' => self::ACTION_SEND_CODE,
                'status' => self::STATUS_FAILED
            ])->count(),
            'verify_code_success' => (int)$query->andWhere([
                'action' => self::ACTION_VERIFY_CODE,
                'status' => self::STATUS_SUCCESS
            ])->count(),
            'verify_code_failed' => (int)$query->andWhere([
                'action' => self::ACTION_VERIFY_CODE,
                'status' => self::STATUS_FAILED
            ])->count(),
            'last_attempt' => $query->max('created_at'),
        ];
    }
    
    /**
     * Clean old logs (automatic cleanup of old records)
     */
    public static function cleanupOldLogs($daysToKeep = 30)
    {
        $timeThreshold = time() - ($daysToKeep * 24 * 3600);
        
        return self::deleteAll(['<', 'created_at', $timeThreshold]);
    }
    
    /**
     * Get client IP address
     */
    private static function getClientIp()
    {
        return Yii::$app->request->userIP ?: 'unknown';
    }
    
    /**
     * Get user agent
     */
    private static function getUserAgent()
    {
        $userAgent = Yii::$app->request->userAgent;
        if ($userAgent && strlen($userAgent) > 500) {
            $userAgent = substr($userAgent, 0, 497) . '...';
        }
        return $userAgent ?: 'unknown';
    }
}