<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use ZakharovAndrew\user\models\Api;
use ZakharovAndrew\user\models\AuthJwt;
use ZakharovAndrew\user\models\User;
use yii\web\Response;


class ApiController extends Controller
{
    private $user_id = null;
  
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@', '?'],
                        'matchCallback' => function ($rule, $action) {
            
                            if ($action->id == 'login' || $action->id == 'signup') {
                                return true;
                            }
                            
                            // получим токен из хидера
                            $token = $this->getBearerToken();
                                    
                            //если он пустой или не такой как нам надо, то уведомим пользователя
                            if (empty($token) || !AuthJwt::validateToken($token)) {
                                header('HTTP/1.0 401 Unauthorized');
                                die('{"error":"Wrong TOKEN!"}');
                            }
                            
                            $this->user_id = AuthJwt::validateToken($token);
                            
                            return true;
                        }
                    ],
                ],
            ],
        ];
    }
    
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        
        if (!in_array($action->id, $this->allowedActions())) {
            header('HTTP/1.1 405 Method Not Allowed');
            die('{"error":"Wrong method!"}');
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
               
        return parent::beforeAction($action);
    }
    
    public function allowedActions()
    {
        return ['login', 'signup', 'profile', 'tabs'];
    }
    
    public function actionLogin()
    {   
        $data = $this->getRawData();
        
        if (empty($data->login) || empty($data->password)) {
            header("HTTP/1.0 401 Unauthorized");
            return ["error" => "Wrong login or password!"];
        }
        
        $access_token = Api::login($data->login, $data->password);
        //связка не найдена
        if ($access_token === false) {
            header("HTTP/1.0 401 Unauthorized");
            return ["error" => "Wrong login or password!"];
        }
        
        return ["access_token" => $access_token, "expires_in" => Yii::$app->getModule('user')->jwtExpiresTime];
    }
    
    public function actionProfile()
    {
        $user = Api::profile($this->user_id);
                
        if (!$user) {
            header("HTTP/1.0 420 Invalid arguments");
            die('{"error": "User not found"}');
        }
        
        return $user;
    }
    
    public function actionSignup()
    {
        $data = $this->getRawData();
        
        if (empty($data->login) || empty($data->email) || empty($data->password)) {
            header("HTTP/1.0 420 Invalid arguments");
             return ["error" => "Missing required fields: login, email or password"];
        }
        
        $result = Api::signup($data->login, $data->email, $data->password);
        if ($result['success']) {
            return ["success" => true, "message" => "User registered successfully"];
        } else {
            header("HTTP/1.0 422 Unprocessable Entity");
            return ["error" => "Registration failed", 'message' => $result['message'] ?? 'Unknown error: '.var_export($result, true)];
        }
    }
    
    /**
     * Получаем Токен из заголовка
     */
    static public function getBearerToken()
    {
        $headers = Yii::$app->request->headers->get('Authorization');
        
        // HEADER: Get the access token from the header
        if (!empty($headers) &&  preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Получаем raw данные и конвертируем в объект
     */
    public function getRawData()
    {
        $rawData = file_get_contents("php://input");
        
        $result = json_decode($rawData);
        if (json_last_error() === JSON_ERROR_NONE) {
            // JSON is valid
            return $result;
        }
        
        header("HTTP/1.0 420 Invalid arguments");
        die('{"error": "Invalid JSON!", "message":"'.json_last_error_msg().'"}');
    }
    
    public function error($code, $text)
    {
        Yii::$app->response->setStatusCode($code);
        return ['error' => $text];
    }
}