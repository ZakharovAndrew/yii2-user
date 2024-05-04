<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\web\IdentityInterface;
use ZakharovAndrew\user\Module;

/**
 * This is the model class for table "crm_users".
 *
 * @property int $id
 * @property string $username Login
 * @property string $auth_key
 * @property string $password
 * @property string|null $password_reset_token
 * @property string $email
 * @property string $name
 * @property string|null $avatar
 * @property string|null $city
 * @property string|null $birthday
 * @property int $status
 * @property int $sex
 * @property string $created_at
 * @property string $updated_at
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_USER = 5;
        
    const STATUS_MANAGER = 20;
    const STATUS_SENIOR_MANAGER = 25;
    const STATUS_ADMIN = 30;
    const STATUS_SENIOR_ADMIN = 35;
    const STATUS_ROOT = 40;
    
    const GROUP_ALLOW_ADM = [self::STATUS_ROOT, self::STATUS_SENIOR_ADMIN, self::STATUS_ADMIN, self::STATUS_SENIOR_MANAGER]; //открыты функции администрирования
    
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password', 'name'], 'required'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => self::className(), 'message' => Module::t('This email is already taken!')],
            
            [['birthday', 'auth_key', 'created_at', 'updated_at'], 'safe'],
            [['status', 'sex'], 'integer'],
            [['username', 'password', 'password_reset_token', 'email', 'name', 'avatar'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['city'], 'string', 'max' => 150],
                        
            [['username'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => Module::t('Login'),
            'auth_key' => 'Auth Key',
            'password' => Module::t('Password'),
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'name' => Module::t('Name'),
            'avatar' => Module::t('Avatar'),
            'city' => Module::t('City'),
            'birthday' => Module::t('Birthday'),
            'status' => Module::t('Status'),
            'sex' => Module::t('Sex'),
            'roles' => Module::t('Roles'),
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    
    public static function getStatusList() {
        return [
            static::STATUS_DELETED => "Заблокированный",
            static::STATUS_USER => Module::t('User'),
            static::STATUS_MANAGER => Module::t('Manager'),
            static::STATUS_SENIOR_MANAGER => Module::t('Senior manager'),
            static::STATUS_ADMIN => Module::t('Administrator'),
            static::STATUS_SENIOR_ADMIN => Module::t('Senior Administrator'),
            static::STATUS_ROOT => "Root"
        ];
    }
    
    public static function getSexList()
    {
        return [
            0 => Module::t('Not specified'),
            1 => Module::t('Male'),
            2 => Module::t('Female'),
        ];
    }

    /**
     * Checking the ability to perform the action of the selected controller
     *
     * @param int $user_id
     * @param int $controller_id
     * @param int $action
     */
    public static function  isActionAllowed($user_id, $controller_id, $action)
    {
        $roles_id = \yii\helpers\ArrayHelper::getColumn(
                UserRoles::find()->select('role_id')->where(['user_id' => $user_id])->asArray()->all(),
                'role_id'
            );
        
        $roles = \yii\helpers\ArrayHelper::getColumn(
                    Roles::find()
                    ->select('code')
                    ->where(['IN', 'id', $roles_id])
                    ->asArray()
                    ->all(),
                    'code'
                );
        
        if (in_array('admin', $roles)) {
            return true;
        }
        
        //other checking
        
        return false;    
    }
    
    /**
     * Получаем все доступные для данного пользователя статусы
     */
    public function getAllowedStatusList(){
        return array_flip(array_filter(array_flip(self::statusLabelsRu()), function($v){
            return $v < $this->status;
        }));
    }
    
    public function validateStatus($attribute, $params) {
        $user = self::findOne(Yii::$app->user->id);
        if ($this->id != Yii::$app->user->id) {
            if ($user && ($this->status > $user->status || $this->id != $user->id && $this->status == $user->status)) {
                $this->addError('status', 'Нельзя ставить пользователю статус выше или равный вашему!');
            }
        }
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(["id" => $id, ['!=', 'status', static::STATUS_DELETED]]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()->where(["username" => $username])
                ->andWhere(['!=', 'status', self::STATUS_DELETED])
                ->one();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    
    /**
     * Email validation
     * 
     * @param type $attribute
     * @param type $params
     */
    public function validateEmail($attribute, $params)
    {
        $user = User::find()->where(["email" => $this->email])->andFilterWhere(['!=', 'id', $this->id])->andWhere(['not', ['status' => [static::STATUS_DELETED]]])->One();
        if ($user) {
            $this->addError($attribute, "Адрес почты {$this->email} уже занят");
        }
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {

        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
        return true;
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::find()->where(["password_reset_token" => $token])->andWhere(['!=', 'status', static::STATUS_DELETED])->One();
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
        
        return true;
    }

    /**
     * Find a user by email
     * 
     * @param string $email Email for search
     * @return mixed
     */
    public static function findByEmail($email)
    {
        return static::find()->where('username=:email OR email=:email', [":email"=>$email])->one();
    }
    
    /**
     * Getting user status (with cache for an hour)
     * 
     * @param object $user User object
     * @return int User status
     */
    public static function getUserStatus($user)
    {       
        if (!is_object($user)){
            $user = \app\models\User::find()->where(['id' => $user])->cache(1*3600)->one();
        }
        
        if (!$user) {
            return 0;
        }

        return $user->status;
    }
    
    public function getAvatarUrl()
    {
        if (empty($this->avatar)) {
            return '/uploaded_files/no-avatar.jpg';
        }
        
        return '/uploaded_files/'.$this->avatar.'_img_medium.jpg';
    }
    
    /**
     * Get user name
     * @return string
     */
    public function getName(){
        return !empty($this->name) ? $this->name : $this->username;
    }
        
    /**
     * Get a list of users by status
     */
    public static function getUsersByStatus($status_id)
    {
        return Yii::$app->cache->getOrSet('get_users_list'.$status_id, function () {
            return \yii\helpers\ArrayHelper::map(
                    self::find()
                    ->where(['in','status', self::GROUP_ALLOW_ADM])
                    ->asArray()
                    ->all(), 'id', 'name');
        }, 3600);
    }
    
    /**
     * Generating a new password
     * @param int $length - new password length
     * @return string new password
     */
    public static function genPassword($length = 10)
    {
        $chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP!@#$%&*?";
        $length = intval($length);
        $size = strlen($chars) - 1;
        $password = "";
        while($length--) {
            $password .= $chars[rand(0, $size)];
        }
        
        return $password;
    }

    /**
     * Send an email with a password
     * 
     * @param string $password
     * @param string $action
     * @return bool whether the email was send
     */
    public function sendPasswordEmail($password, $action = 'create')
    {
        $params = [
            'create' => ["view" => "password-new-html", "subject" => "Регистрация в "],
            'reset' =>  ["view" => "password-reset-html", "subject" => "Сброс пароля для "], //при сбросе пароля админом
        ];
        
        $action = (isset($params[$action]) ? $action : 'create');
        
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => '@vendor/zakharov-andrew/yii2-user/src/mail/'. $params[$action]['view']],
                ['user' => $this, 'password' => $password]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject($params[$action]['subject'] . ' '.Yii::$app->name)
            ->send();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
}
