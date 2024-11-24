<?php

/**
 * @copyright Copyright &copy; Andrey Zakharov, 2023 - 2024
 * @package yii2-user
 * @version 0.5.7
 */

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;
use ZakharovAndrew\user\Module;

/**
 * Class for logging authorization attempts and blocking IP access if unsuccessful
 *
 * @author Andrey Zakharov
 * @since 0.5.7
 */
class LoginAttempt extends ActiveRecord
{
    const MAX_ATTEMPT = 3;
    
    public static function tableName()
    {
        return 'login_attempts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'ip_address'], 'required'],
            [['attempt_time'], 'safe'],
            [['successful'], 'boolean'],
            [['username'], 'string', 'max' => 255],
            [['ip_address'], 'string', 'max' => 45], // Sufficient length for IPv6
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => Module::t('Username'),
            'ip_address' => Module::t('IP Address'),
            'attempt_time' => Module::t('Attempt Time'),
            'successful' => Module::t('Successful Attempt'),
        ];
    }
    
    /**
     * Method to check if the IP address is blocked
     *
     * @param string $ipAddress User IP address
     * @return boolean
     */
    public static function isBlockedByIp($ipAddress)
    {
        $count = static::find()
            ->where(['ip_address' => $ipAddress, 'successful' => false])
            ->andWhere(['>', 'attempt_time', new \yii\db\Expression('NOW() - INTERVAL 1 HOUR')])
            ->count();

        return $count >= static::MAX_ATTEMPT; // Block after 3 unsuccessful attempts
    }
    
    /**
     * Method to log the login attempt
     * 
     * @param string $username user login
     * @param string $ipAddress User IP address
     * @param boolean $successful
     * @return boolean
     */
    public static function logLoginAttempt($username, $ipAddress, $successful)
    {
        $loginAttempt = new self();
        $loginAttempt->username = $username;
        $loginAttempt->ip_address = $ipAddress;
        $loginAttempt->attempt_time = date('Y-m-d H:i:s');
        $loginAttempt->successful = $successful;
        
        return $loginAttempt->save(); // Save the login attempt to the database
    }
}
