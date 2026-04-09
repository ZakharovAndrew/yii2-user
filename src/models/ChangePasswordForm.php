<?php


namespace ZakharovAndrew\user\models;

use Yii;
use yii\base\Model;
use ZakharovAndrew\user\Module;

/**
 * Password reset request form
 * 
 * @link https://github.com/ZakharovAndrew/yii2-user/
 * @copyright Copyright (c) 2023-2024 Zakharov Andrew
 */
class ChangePasswordForm extends Model
{
    /**
     * @var string Current password (for verification)
     */
    public $old_password;
    
    /**
     * @var string New password
     */
    public $new_password;
    
    /**
     * @var string New password confirmation
     */
    public $new_password_repeat;
    
    /**
     * @var IdentityInterface|null User identity
     */
    private $_user;

    /**
     * Constructor with dependency injection
     * 
     * @param IdentityInterface|null $user User identity (null = current user)
     * @param array $config
     */
    public function __construct($user = null, $config = [])
    {
        if ($user === null) {
            $user = Yii::$app->user->identity;
        }
        $this->_user = $user;
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['old_password', 'new_password', 'new_password_repeat'], 'required', 'message' => Module::t('The field must not be empty.')],
            [['new_password', 'new_password_repeat'], 'string', 'min' => 6],
            [
                'new_password_repeat', 'compare', 'compareAttribute' => 'new_password',
                'message' => Module::t('Password confirmation does not match.'), 'skipOnEmpty' => true
            ],
            
            // Old password validation
            ['old_password', 'validateOldPassword'],
            
            // Prevent setting the same password
            ['new_password', 'validateNotSameAsOld'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'old_password' => Module::t('Current password'),
            'new_password' => Module::t('New password'),
            'new_password_repeat' => Module::t('Repeat password')
        ];
    }
    
    /**
     * Validates the current password
     * 
     * @param string $attribute
     * @param array $params
     */
    public function validateOldPassword($attribute, $params)
    {
        $user = Yii::$app->user->identity;
        
        if (!$user->validatePassword($this->$attribute)) {
            $this->addError($attribute, Module::t('Invalid current password'));
        }
    }
    
    /**
     * Validates that new password is different from old password
     * 
     * @param string $attribute
     * @param array $params
     */
    public function validateNotSameAsOld($attribute, $params)
    {
        if (!$this->hasErrors() && $this->_user && $this->new_password) {
            // Check if new password matches old password
            if ($this->_user->validatePassword($this->new_password)) {
                $this->addError($attribute, Module::t('New password must be different from the current password'));
            }
        }
    }
}