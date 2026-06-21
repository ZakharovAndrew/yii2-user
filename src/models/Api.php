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
     * @param array $fields Fields to select (default: id, username, name, email, avatar, coins, sex, status)
     * @return mixed User object or null if not found
     */
    static function profile($id, $fields = ['id', 'username', 'name', 'email', 'avatar', 'coins', 'sex', 'status'])
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
     * Update user username
     * 
     * @param int $userId User ID
     * @param string $newUsername New username
     * @return array Result array with success status and message
     */
    static function updateUsername($userId, $newUsername)
    {
        $userClass = Yii::$app->getModule('user')->apiUserClass;

        // Find user by ID
        $user = $userClass::find()
            ->where(['id' => $userId])
            ->andWhere(['!=', 'status', $userClass::STATUS_DELETED])
            ->one();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        // Проверяем, не занят ли новый username другим пользователем
        if ($user->username !== $newUsername) {
            $existingUser = $userClass::find()
                ->where(['username' => $newUsername])
                ->andWhere(['!=', 'id', $userId])
                ->one();

            if ($existingUser) {
                return [
                    'success' => false,
                    'message' => 'This username is already taken'
                ];
            }
        }

        // Проверяем минимальную длину username
        if (strlen($newUsername) < 3) {
            return [
                'success' => false,
                'message' => 'Username must be at least 3 characters long'
            ];
        }

        // Проверяем допустимые символы (только буквы, цифры, подчеркивания)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $newUsername)) {
            return [
                'success' => false,
                'message' => 'Username can only contain letters, numbers and underscores'
            ];
        }

        // Проверяем максимальную длину
        if (strlen($newUsername) > 50) {
            return [
                'success' => false,
                'message' => 'Username cannot exceed 50 characters'
            ];
        }

        // Сохраняем старый username для логов
        $oldUsername = $user->username;

        // Обновляем username
        $user->username = $newUsername;

        if (!$user->save()) {
            Yii::error('Username update failed: ' . print_r($user->errors, true));
            $errors = $user->errors;

            return [
                'success' => false,
                'message' => array_shift($errors)[0] ?? 'Failed to update username'
            ];
        }

        // Логируем изменение username (если у вас есть система логов)
        Yii::info("User {$userId} changed username from '{$oldUsername}' to '{$newUsername}'");

        return [
            'success' => true,
            'message' => 'Username successfully updated',
            'new_username' => $newUsername
        ];
    }
    
    /**
     * Update user profile
     * 
     * @param int $userId User ID
     * @param array $data Profile data to update
     * @return array Result array with success status and message
     */
    static function updateProfile($userId, $data)
    {
        $userClass = Yii::$app->getModule('user')->apiUserClass;

        // Find user by ID
        $user = $userClass::find()
            ->where(['id' => $userId])
            ->andWhere(['!=', 'status', $userClass::STATUS_DELETED])
            ->one();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        // Разрешенные поля для обновления
        $allowedFields = ['name', 'sex', 'avatar'];

        foreach ($data as $field => $value) {
            // Пропускаем поля, которые не разрешены для обновления
            if (!in_array($field, $allowedFields) || !$user->hasAttribute($field)) {
                continue;
            }
            
            // если пол не находится в списке доступных
            if ($field === 'sex' && !isset($userClass::getSexList()[$value])) {
                return [
                    'success' => false,
                    'message' => 'Failed to update profile'
                ];
            }
            
            $user->$field = $value;
        }

        if (!$user->save()) {
            Yii::error('Profile update failed: ' . print_r($user->errors, true));
            $errors = $user->errors;

            return [
                'success' => false,
                'message' => array_shift($errors)[0] ?? 'Failed to update profile'
            ];
        }

        return [
            'success' => true,
            'message' => 'Profile successfully updated',
        ];
    }

    /**
     * Get list of users with pagination and filtering
     * 
     * @param int $page Page number (default: 1)
     * @param int $limit Number of records per page (default: 20)
     * @param array $filters Filter parameters (status, role, search, date_from, date_to, role_id, subject_id)
     * @return array Result with users list and pagination info
     */
    static function getUsers($page = 1, $limit = 20, $filters = [])
    {
        $userClass = Yii::$app->getModule('user')->apiUserClass;
        
        // Build base query
        $query = $userClass::find()
            ->select([
                'id', 
                'username', 
                'name', 
                'email', 
                'avatar', 
                'sex', 
                'status', 
                'created_at',
                'updated_at'
            ])
            ->andWhere(['!=', 'status', $userClass::STATUS_DELETED]);
        
        // Apply status filter
        if (!empty($filters['status'])) {
            $query->andWhere(['status' => $filters['status']]);
        }
        
        // Apply search filter (username, name, email)
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->andWhere([
                'or',
                ['like', 'username', $search],
                ['like', 'name', $search],
                ['like', 'email', $search]
            ]);
        }
        
        // Apply date range filters
        if (!empty($filters['date_from'])) {
            $query->andWhere(['>=', 'created_at', $filters['date_from']]);
        }
        
        if (!empty($filters['date_to'])) {
            $query->andWhere(['<=', 'created_at', $filters['date_to']]);
        }
        
        // Apply role filter using UserRoles model
        if (!empty($filters['role'])) {
            $roleFilter = $filters['role'];
            
            // Get users with specific role
            $subQuery = UserRoles::find()
                ->select('user_roles.user_id')
                ->leftJoin('roles', 'user_roles.role_id = roles.id');
            
            // Filter by role code or ID
            if (is_numeric($roleFilter)) {
                $subQuery->andWhere(['user_roles.role_id' => $roleFilter]);
            } else {
                $subQuery->andWhere(['roles.code' => $roleFilter]);
            }
            
            // Apply subject filter if specified
            if (!empty($filters['subject_id'])) {
                $subQuery->andWhere([
                    'or',
                    ['user_roles.subject_id' => $filters['subject_id']],
                    ['user_roles.subject_id' => null]
                ]);
            }
            
            $userIds = ArrayHelper::getColumn($subQuery->asArray()->all(), 'user_id');
            
            if (empty($userIds)) {
                // No users with this role
                return [
                    'success' => true,
                    'data' => [],
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total_count' => 0,
                        'total_pages' => 0
                    ]
                ];
            }
            
            $query->andWhere(['in', 'id', $userIds]);
        }
        
        // Filter by role ID directly (alternative filter)
        if (!empty($filters['role_id'])) {
            $userIds = UserRoles::find()
                ->select('user_roles.user_id')
                ->where(['user_roles.role_id' => $filters['role_id']]);
            
            if (!empty($filters['subject_id'])) {
                $userIds->andWhere([
                    'or',
                    ['user_roles.subject_id' => $filters['subject_id']],
                    ['user_roles.subject_id' => null]
                ]);
            }
            
            $userIds = ArrayHelper::getColumn($userIds->asArray()->all(), 'user_id');
            
            if (empty($userIds)) {
                return [
                    'success' => true,
                    'data' => [],
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total_count' => 0,
                        'total_pages' => 0
                    ]
                ];
            }
            
            $query->andWhere(['in', 'id', $userIds]);
        }
        
        // Filter by multiple role IDs
        if (!empty($filters['role_ids']) && is_array($filters['role_ids'])) {
            $userIds = UserRoles::find()
                ->select('user_roles.user_id')
                ->where(['in', 'user_roles.role_id', $filters['role_ids']]);
            
            if (!empty($filters['subject_id'])) {
                $userIds->andWhere([
                    'or',
                    ['user_roles.subject_id' => $filters['subject_id']],
                    ['user_roles.subject_id' => null]
                ]);
            }
            
            $userIds = ArrayHelper::getColumn($userIds->asArray()->all(), 'user_id');
            
            if (empty($userIds)) {
                return [
                    'success' => true,
                    'data' => [],
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total_count' => 0,
                        'total_pages' => 0
                    ]
                ];
            }
            
            $query->andWhere(['in', 'id', $userIds]);
        }
        
        // Apply sorting
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'DESC';
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['id', 'username', 'name', 'email', 'status', 'created_at', 'updated_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy([$sortField => $sortDirection === 'ASC' ? SORT_ASC : SORT_DESC]);
        } else {
            $query->orderBy(['created_at' => SORT_DESC]);
        }
        
        // Get total count for pagination
        $totalCount = $query->count();
        
        // Apply pagination
        $offset = ($page - 1) * $limit;
        $users = $query->offset($offset)
            ->limit($limit)
            ->all();
        
        // Enrich users with roles information
        $resultData = [];
        foreach ($users as $user) {
            $userData = $user->toArray();
            
            // Get user roles with their details
            $userRoles = UserRoles::getUserRoles($user->id);
            $userData['roles'] = $userRoles;
            
            // Get role IDs for quick access
            $roleIds = UserRoles::getUserRolesIds($user->id);
            $userData['role_ids'] = $roleIds;
            
            // Get full role objects (if needed)
            if (!empty($filters['with_full_roles'])) {
                $fullRoles = Roles::getRolesByUserId($user->id);
                $userData['full_roles'] = $fullRoles;
            }
            
            // Add additional user data if requested
            if (!empty($filters['with_profile'])) {
                $userData['profile'] = self::profile($user->id);
            }
            
            $resultData[] = $userData;
        }
        
        return [
            'success' => true,
            'data' => $resultData,
            'pagination' => [
                'page' => (int)$page,
                'limit' => (int)$limit,
                'total_count' => (int)$totalCount,
                'total_pages' => (int)ceil($totalCount / $limit)
            ],
            'filters_applied' => $filters
        ];
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
