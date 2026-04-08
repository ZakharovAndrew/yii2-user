<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;
use ZakharovAndrew\user\Module;

/**
 * Friendship model
 *
 * @property int $id
 * @property int $user_id User who sent the request
 * @property int $friend_id User who received the request
 * @property int $status Status: 0 - pending, 1 - accepted, 2 - rejected, 3 - blocked
 * @property string|null $responded_at Response time
 * @property string $created_at
 * 
 * @property User $user User who sent the request
 * @property User $friend User who received the request
 */
class Friendship extends ActiveRecord
{
    const STATUS_PENDING = 0;   // Pending confirmation
    const STATUS_ACCEPTED = 1;  // Accepted
    const STATUS_REJECTED = 2;  // Rejected
    const STATUS_BLOCKED = 3;   // Blocked

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'friendships';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'friend_id'], 'required'],
            [['user_id', 'friend_id', 'status'], 'integer'],
            [['responded_at', 'created_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['friend_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['friend_id' => 'id']],
            ['status', 'in', 'range' => array_keys(self::getStatusList())],
            ['friend_id', 'compare', 'compareAttribute' => 'user_id', 'operator' => '!=', 'message' => Module::t('You cannot add yourself as a friend')],
            [['user_id', 'friend_id'], 'unique', 'targetAttribute' => ['user_id', 'friend_id'], 'message' => Module::t('Friendship request already exists')],
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
            'friend_id' => Module::t('Friend'),
            'status' => Module::t('Status'),
            'responded_at' => Module::t('Responded At'),
            'created_at' => Module::t('Created At')
        ];
    }

    /**
     * Get status list
     * 
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_PENDING => Module::t('Pending'),
            self::STATUS_ACCEPTED => Module::t('Accepted'),
            self::STATUS_REJECTED => Module::t('Rejected'),
            self::STATUS_BLOCKED => Module::t('Blocked'),
        ];
    }

    /**
     * Get status name
     * 
     * @return string
     */
    public function getStatusName()
    {
        $statuses = self::getStatusList();
        return isset($statuses[$this->status]) ? $statuses[$this->status] : Module::t('Unknown');
    }

    /**
     * Get user who sent the request
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Get user who received the request
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getFriend()
    {
        return $this->hasOne(User::class, ['id' => 'friend_id']);
    }

    /**
     * Check if request is pending
     * 
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request is accepted
     * 
     * @return bool
     */
    public function isAccepted()
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    /**
     * Check if request is rejected
     * 
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if request is blocked
     * 
     * @return bool
     */
    public function isBlocked()
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    /**
     * Accept friend request
     * 
     * @return bool
     */
    public function accept()
    {
        $this->status = self::STATUS_ACCEPTED;
        $this->responded_at = date('Y-m-d H:i:s');
        return $this->save();
    }

    /**
     * Reject friend request
     * 
     * @return bool
     */
    public function reject()
    {
        $this->status = self::STATUS_REJECTED;
        $this->responded_at = date('Y-m-d H:i:s');
        return $this->save();
    }

    /**
     * Block user
     * 
     * @return bool
     */
    public function block()
    {
        $this->status = self::STATUS_BLOCKED;
        $this->responded_at = date('Y-m-d H:i:s');
        return $this->save();
    }

    /**
     * Cancel friend request
     * 
     * @return bool
     */
    public function cancel()
    {
        return $this->delete();
    }

    /**
     * Check if friendship exists
     * 
     * @param int $userId First user ID
     * @param int $friendId Second user ID
     * @return bool
     */
    public static function exists($userId, $friendId)
    {
        return self::find()
            ->where(['user_id' => $userId, 'friend_id' => $friendId])
            ->orWhere(['user_id' => $friendId, 'friend_id' => $userId])
            ->exists();
    }

    /**
     * Get friendship between two users
     * 
     * @param int $userId First user ID
     * @param int $friendId Second user ID
     * @return Friendship|null
     */
    public static function getFriendship($userId, $friendId)
    {
        return self::find()
            ->where(['user_id' => $userId, 'friend_id' => $friendId])
            ->orWhere(['user_id' => $friendId, 'friend_id' => $userId])
            ->one();
    }

    /**
     * Create friend request
     * 
     * @param int $userId User who sends request
     * @param int $friendId User who receives request
     * @return Friendship|array Returns model or error array
     */
    public static function createRequest($userId, $friendId)
    {
        // Check if request already exists
        if (self::exists($userId, $friendId)) {
            return ['error' => Module::t('Friendship request already exists')];
        }

        // Check if user tries to add himself
        if ($userId == $friendId) {
            return ['error' => Module::t('You cannot add yourself as a friend')];
        }

        $model = new self();
        $model->user_id = $userId;
        $model->friend_id = $friendId;
        $model->status = self::STATUS_PENDING;

        if ($model->save()) {
            return $model;
        }

        return ['error' => Module::t('Failed to send friend request'), 'errors' => $model->errors];
    }

    /**
     * After save
     * 
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Send notification for new request
        if ($insert) {
            $this->sendNotification();
        }
        
        // Send notification for accepted request
        if (isset($changedAttributes['status']) && $this->status == self::STATUS_ACCEPTED) {
            $this->sendAcceptanceNotification();
            
            // Update friends count
            $this->updateFriendsCount($this->user_id);
            $this->updateFriendsCount($this->friend_id);
        }
    }

    /**
     * After delete
     */
    public function afterDelete()
    {
        parent::afterDelete();
        
        // Update friends count if accepted friendship was deleted
        if ($this->status == self::STATUS_ACCEPTED) {
            $this->updateFriendsCount($this->user_id);
            $this->updateFriendsCount($this->friend_id);
        }
    }

    /**
     * Update friends count for user
     * 
     * @param int $userId
     */
    protected function updateFriendsCount($userId)
    {
        $count = Friendship::find()
            ->where(['status' => self::STATUS_ACCEPTED])
            ->andWhere([
                'or',
                ['user_id' => $userId],
                ['friend_id' => $userId]
            ])
            ->count();
        
        User::updateAll(['friends_count' => $count], ['id' => $userId]);
    }

    /**
     * Send notification about new friend request
     */
    protected function sendNotification()
    {

        if ($this->friend && $this->friend->email) {
            Yii::$app->mailer
                ->compose(['html' => '@vendor/zakharov-andrew/yii2-user/src/mail/new-friend-request'], [
                    'name' => $this->user->name,
                    'user' => $this->friend
                ])
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setTo($this->friend->email)
                ->setSubject(Module::t('New friend request from {name}', ['name' => $this->user->name]))
                ->send();
        }
    }

    /**
     * Send notification about accepted request
     */
    protected function sendAcceptanceNotification()
    {
        if ($this->user && $this->user->email) {
            Yii::$app->mailer
                ->compose([
                    'html' => '@vendor/zakharov-andrew/yii2-user/src/mail/friend-request-accepted',
                    //'text' => '@vendor/zakharov-andrew/yii2-user/src/mail/friend-request-accepted-text'
                ], [
                    'name' => $this->friend->name,
                    'user' => $this->user
                ])
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setTo($this->user->email)
                ->setSubject(Module::t('Your friend request has been accepted'))
                ->send();
        }
    }
}