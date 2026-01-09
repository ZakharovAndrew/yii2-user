<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\models\AuthJwt;
use ZakharovAndrew\user\models\VerificationLog;

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
     * @param array $fields Fields to select (default: id, username, name, email, sex, status)
     * @return mixed User object or null if not found
     */
    static function profile($id, $fields = ['id', 'username', 'name', 'email', 'sex', 'status'])
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
        
        // Проверяем, существует ли уже пользователь с таким email
        $existingUser = $userClass::find()
            ->where(['email' => $email])
            ->orWhere(['username' => $username])
            ->one();

        if ($existingUser) {
            if ($existingUser->email === $email) {
                return ['success' => false, 'message' => 'Email already registered'];
            }
            if ($existingUser->username === $username) {
                return ['success' => false, 'message' => 'Username already taken'];
            }
        }
        
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
     * Resets password.
     */
    static function resetPassword($username, $email)
    {
        $userClass = Yii::$app->getModule('user')->apiUserClass;
        
        // Проверяем, существует ли уже пользователь с таким email
        $existingUser = $userClass::find()
            ->where(['email' => $email])
            ->andWhere(['username' => $username])
            ->one();
        
        if (!$existingUser) {    
            return ['success' => false, 'message' => 'A user with the specified login and email was not found.'];
        }
        
        $result = $existingUser->sendEmailResetPassword();
        
        return ['success' => $result];
    }
    
    /**
     * Resend verification email
     * 
     * @param int $user_id User ID
     * @return array Result array with success status and message
     */
    static function resendVerification($user_id)
    {
        $userClass = Yii::$app->getModule('user')->apiUserClass;

        // Find user by ID
        $user = $userClass::find()
                ->where(['id' => $user_id])
                ->andWhere(['status' => $userClass::STATUS_INACTIVE])
                ->one();

        if (!$user) {
            VerificationLog::log(
                $user_id, // user_id может быть null
                '',
                VerificationLog::ACTION_SEND_CODE,
                VerificationLog::STATUS_FAILED,
                'User not found or already verified'
            );
        
            return [
                'success' => false,
                'message' => 'User not found or already verified'
            ];
        }

        // Resend verification email
        $status = $user->resendVerification();
        if ($status['success']) {
            VerificationLog::log(
                $user->id, // user_id может быть null
                $user->email,
                VerificationLog::ACTION_SEND_CODE,
                VerificationLog::STATUS_SUCCESS,
                'Verification email sent successfully'
            );
            
            return [
                'success' => true,
                'message' => 'Verification email sent successfully'
            ];
        }

        return [
            'success' => false,
            'message' => $status['message']
        ];
    }
    
    /**
     * Verify email with verification code
     * 
     * @param string $email User email
     * @param string $code Verification code
     * @return array Result array with success status and message
     */
    static function verifyEmail($email, $code)
    {
        $userClass = Yii::$app->getModule('user')->apiUserClass;
        
        return $userClass::verifyEmail($email, $code);
    }
    
    /**
     * Get client IP address
     */
    public static function getUserIP()
    {
        return Yii::$app->request->userIP ?: 'unknown';
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