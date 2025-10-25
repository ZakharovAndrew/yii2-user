<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_controller_log".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $controller
 * @property string $action
 * @property string|null $method
 * @property string|null $url
 * @property string|null $request_params
 * @property int $response_code
 * @property float $execution_time
 * @property string $created_at
 * @property string $ip_address
 * @property string|null $user_agent
 */
class UserControllerLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_controller_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'response_code'], 'integer'],
            [['controller', 'action'], 'required'],
            [['request_params'], 'string'],
            [['execution_time'], 'number'],
            [['created_at'], 'safe'],
            [['controller', 'action', 'method', 'url', 'ip_address', 'user_agent'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'controller' => 'Controller',
            'action' => 'Action',
            'method' => 'Method',
            'url' => 'URL',
            'request_params' => 'Request Params',
            'response_code' => 'Response Code',
            'execution_time' => 'Execution Time',
            'created_at' => 'Created At',
            'ip_address' => 'IP Address',
            'user_agent' => 'User Agent',
        ];
    }

    /**
     * Log controller request
     */
    public static function logRequest($controller, $action, $startTime, $response = null)
    {
        try {
            $log = new self();
            $log->user_id = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
            $log->controller = $controller;
            $log->action = $action;
            $log->method = Yii::$app->request->method;
            $log->url = Yii::$app->request->url;
            $log->ip_address = Yii::$app->request->userIP;
            $log->user_agent = Yii::$app->request->userAgent;
            
            // Log request parameters (excluding sensitive data)
            $requestParams = self::filterSensitiveData(Yii::$app->request->getBodyParams());
            if (empty($requestParams)) {
                $requestParams = Yii::$app->request->getQueryParams();
            }
            $log->request_params = json_encode($requestParams, JSON_UNESCAPED_UNICODE);
            
            // Log response code
            if ($response !== null) {
                $log->response_code = $response->statusCode;
            }
            
            $log->execution_time = microtime(true) - $startTime;
            $log->created_at = date('Y-m-d H:i:s');
            
            $log->save();
            
            
        } catch (\Exception $e) {
            Yii::error('Failed to log controller request: ' . $e->getMessage(), 'controller-log');
        }
    }
    
    /**
     * Filter sensitive data (passwords, tokens, etc.)
     */
    private static function filterSensitiveData($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        
        $sensitiveFields = [
            'password', 'password_hash', 'password_repeat', 'auth_key',
            'access_token', 'refresh_token', 'token', 'secret',
            'credit_card', 'cvv', 'card_number', 'security_code'
        ];
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::filterSensitiveData($value);
            } elseif (in_array(strtolower($key), $sensitiveFields)) {
                $data[$key] = '***HIDDEN***';
            }
        }
        
        return $data;
    }
    
    /**
     * Get related user
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}