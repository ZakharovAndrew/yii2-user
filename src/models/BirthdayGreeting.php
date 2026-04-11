<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;
use ZakharovAndrew\user\Module;

/**
 * BirthdayGreeting model
 * 
 * Manages birthday greetings sent between users
 * 
 * @property int $id Unique greeting identifier
 * @property int $user_id ID of the user receiving the greeting
 * @property int $author_id ID of the user sending the greeting
 * @property string $message Greeting text message
 * @property string $created_at Timestamp when greeting was created
 * @property int $is_read Status flag (0 - unread, 1 - read)
 * 
 * @property-read User $user User who receives the greeting
 * @property-read User $author User who sent the greeting
 * 
 * @author Andrew Zakharov
 * @since 1.0
 */
class BirthdayGreeting extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'birthday_greeting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'author_id'], 'required'],
            [['user_id', 'author_id'], 'integer'],
            [['created_at'], 'safe'],
            ['message', 'string', 'min' => 5, 'max' => 1000],
            ['message', 'filter', 'filter' => 'strip_tags'],
            [['is_read'], 'boolean'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Module::t('User'),
            'author_id' =>  Module::t('Author'),
            'message' => Module::t('Text'),
            'created_at' => Module::t('Creation date'),
        ];
    }

    /**
     * Gets the user who receives the greeting
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets the user who sent the greeting
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }
    
    /**
     * Get formatted creation date with human-readable relative dates
     * 
     * Returns date in format "Today 14:30:00", "Yesterday 10:15:00", or "25.12.2024 09:00:00"
     * 
     * @return string Formatted date string
     */
    public function getCreatedAt()
    {
        $createdAt = new \DateTime($this->created_at);
        $now = new \DateTime();
        $diff = $now->diff($createdAt);
    
        if ($diff->days == 0) {
            return Module::t('Today') . ' ' . $createdAt->format('H:i:s');
        } elseif ($diff->days == 1) {
            return Module::t('Yesterday') . ' ' . $createdAt->format('H:i:s');
        }
    
        return $createdAt->format('d.m.Y H:i:s');
    }
    
    /**
     * Send birthday greeting email to the recipient
     * 
     * @return bool Whether the email was sent successfully
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne($this->user_id);
 
        if (!$user) {
            return false;
        }
 
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => '@vendor/zakharov-andrew/yii2-user/src/mail/happy-birthday-html'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($user->email)
            ->setSubject(Module::t('Happy Birthday') . '!')
            ->send();
    }
}
