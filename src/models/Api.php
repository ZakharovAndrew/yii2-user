<?php

namespace ZakharovAndrew\user\models;

use ZakharovAndrew\user\models\AuthJwt;
use ZakharovAndrew\user\models\User;

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
        // Find user by login excluding deleted accounts
        $user = User::findOne(["login" => $login, ['!=', 'status', User::STATUS_DELETED]]);
        
        // Authentication failed: user not found or invalid password
        if (!$user || !$user->validatePassword($password)) {
            return false;
        }
        
        // Generate and return JWT token for the authenticated user
        return AuthJwt::generateToken($user['id']);
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