<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\base\Model;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\User;

/**
 * SignupForm is the model behind the sign up form.
 *
 * @property-read User|null $user
 *
 */
class SignupForm extends Model
{
    /**
     * @var string User login
     */
    public $username;
        
    /**
     * @var string User password
     */
    public $password;
    
    /**
     * @var string User name
     */
    public $name;
    
    /**
     * @var string User email
     */
    public $email;
    

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'name', 'email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::className(), 'message' => Module::t('This email is already taken!')],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }
    

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => Module::t('Login'),
            'password' => Module::t('Password'),
            'email' => 'Email',
            'name' => Module::t('Name'),
            'phone' => Module::t('Phone'),
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

}
