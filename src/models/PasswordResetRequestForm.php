<?php
 
namespace ZakharovAndrew\user\models;
 
use Yii;
use yii\base\Model;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\Module;
 
/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;
    public $username;
 
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email','username'], 'trim'],
            [['email','username'], 'required', 'message' => Module::t('The field must not be empty.')],
            ['email', 'email'],
            [['email','username'], 'validateUserData'],
        ];
    }

    public function validateUserData($attribute)
    {
        
        $user = User::find()
                ->where(['email' => $this->email])
                ->andWhere(['username' => $this->username])
                ->andWhere(['!=', 'status', User::STATUS_DELETED])
                ->one();

        if (!$user) {
            $this->addError($attribute, Module::t('A user with the specified login and email was not found.'));
            return false;
        } else {
            return true;
        }
    }
 
    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::find()
                ->where(['email' => $this->email, 'username' => $this->username])
                ->andWhere(['!=', 'status', User::STATUS_DELETED])
                ->One();
 
        if (!$user) {
            return false;
        }
 
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }
 
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => '@vendor/zakharov-andrew/yii2-user/src/mail/passwordResetToken-html'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject(Module::t('Reset password for') . ' ' . Yii::$app->name)
            ->send();
    }
 
}
