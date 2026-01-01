<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\models\AuthJwt;

class Api
{
    /**
     * Authenticates user by login and password
     * 
     * @param string $login User login
     * @param string $password User password
     * @return string|false JWT token on success, false on authentication failure
     */
    static function login($login, $password)
    {
        $userClass = Yii::$app->getModule('user')->apiUserClass;
        
        // Find user by login excluding deleted accounts
        $user = $userClass::find()->where(["username" => $login])->andWhere(['!=', 'status', $userClass::STATUS_DELETED])->one();
        
        // Authentication failed: user not found or invalid password
        if (!$user || !$user->validatePassword($password)) {
            return false;
        }
        
        // Generate and return JWT token for the authenticated user
        return AuthJwt::generateToken($user['id']);
    }
    
    /**
     * Get user profile by ID with selected fields
     * 
     * @param int $id User ID
     * @param array $fields Fields to select (default: id, username, name)
     * @return mixed User object or null if not found
     */
    static function profile($id, $fields = ['id', 'username', 'name', 'sex'])
    {
        $userClass = Yii::$app->getModule('user')->apiUserClass;
        
        $user = $userClass::find()
                ->select($fields)
                ->where(['id' => $id])
                ->one();
        
        return $user;
    }
    
    /**
     * Register a new user account
     * 
     * @param string $username User login/username
     * @param string $name User name
     * @param string $email User email address
     * @param string $password User password
     * @param int $sex
     * @return array|false Returns user data with token on success, false on failure
     */
    static function signup($username, $name, $email, $password, $sex = null)
    {
        $userClass = Yii::$app->getModule('user')->apiUserClass;
        
        // Create new user model instance
        $model = new $userClass([
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'sex' => $sex,
            'status' => $userClass::STATUS_INACTIVE, // Set initial status as inactive
        ]);
        
        // Set password and generate auth key for email verification
        $model->setPassword($password);
        $model->generateEmailVerificationCode();
        
        // Save the user model
        if (!$model->save()) {
            Yii::error('User registration failed: ' . print_r($model->errors, true));
            $errors = $model->errors;
            
            return ['success' => false, 'message' => array_shift($errors)[0] ?? 'Unknown error' ];
        }
        
        // Send email verification if email sending is configured
        if ($model->sendEmailVerification()) {
            return [
                'success' => true,
                'access_token' => AuthJwt::generateToken($model->id),
                'expires_in' => Yii::$app->getModule('user')->jwtExpiresTime,
                'message' => 'User registered successfully'
            ];
            
        }
        
        return ['success' => false, 'message' => 'Mail sending error'];
    }
    
    /**
     * Get client's IP address from various HTTP headers
     * 
     * @return string|false IP address if found, false otherwise
     */
    public static function getUserIP()
    {
        // Check different proxy headers in order of priority
        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            return  $_SERVER['HTTP_X_REAL_IP'];
        } elseif (!empty($_SERVER['HTTP_X_CLIENT_IP'])) {
            return  $_SERVER['HTTP_X_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return false;
    }
    
    /**
     * Validate date format and optional minimum date constraint
     * 
     * @param string $date Date string to validate
     * @param string $format Required date format (default: 'Y-m-d H:i:s')
     * @param string|null $min Minimum allowed date (in the same format)
     * @return boolean True if date is valid and meets constraints
     */
    private static function validateDate($date, $format = 'Y-m-d H:i:s', $min = null)
    {
        // Create DateTime object from the input string
        $d = \DateTime::createFromFormat($format, $date);

        // Check if date matches the format exactly
        if (!$d || $d->format($format) != $date) {
            return false;
        }

        // Validate minimum date constraint if provided
        if ($min) {
            $minDate = \DateTime::createFromFormat($format, $min);
            if (!$minDate || $d < $minDate) {
                return false;
            }
        }

        return true;
    }
}