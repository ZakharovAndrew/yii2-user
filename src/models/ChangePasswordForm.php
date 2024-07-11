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
     * @var string Current password
     */
    public $old_password;
    
    /**
     * @var string New password
     */
    public $new_password;
    
    /**
     * @var string Repeat password
     */
    public $new_password_repeat;

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
                'message' => "Подтверждение пароля должно совпадать.", 'skipOnEmpty' => true
            ],
            ['old_password', 'validateOldPassword']
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
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validateOldPassword($attribute, $params)
    {
        $user = Yii::$app->user->identity;
        
        if (!$user->validatePassword($this->$attribute)) {
            $this->addError($attribute, Module::t('Invalid current password'));
        }
    }
}