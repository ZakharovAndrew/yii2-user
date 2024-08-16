<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\base\Model;
use ZakharovAndrew\user\Module;

/**
 * Email reset request form
 */
class ChangeEmailForm extends Model
{
    /**
     * @var string Current password
     */
    public $password;
    
    /**
     * @var string New Email
     */
    public $new_email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password','new_email'], 'required', 'message' => Module::t('The field must not be empty.')],
            [['new_email'], 'email', 'message' => Module::t('Incorrect E-Mail Address.')],
        ];
    }

    /**
     * Sending an email with instructions
     * 
     * @param object $user
     * @return mixed
     */
    public function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => '@vendor/zakharov-andrew/yii2-user/src/mail/email-change-html'],
                ['model' => $this, 'user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($this->new_email)
            ->setSubject(Module::t('Change email') . ' ' . Yii::$app->name)
            ->send();
    }

}