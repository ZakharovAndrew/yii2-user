<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Reminder model
 * 
 * @property int $id
 * @property int $user_id Reminder recipient
 * @property int $created_by Reminder creator
 * @property string|null $description Description
 * @property string $remind_at Reminder time
 * @property int $status Status: 1-active, 2-completed, 3-cancelled
 * @property string $created_at Creation date
 * 
 * @property User $user
 * @property User $creator
 */
class Reminder extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_CANCELLED = 3;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%reminders}}';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'remind_at'], 'required'],
            [['user_id', 'created_by', 'status'], 'integer'],
            [['description'], 'string'],
            [['remind_at', 'created_at'], 'safe'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['remind_at', 'validateRemindAt'],
        ];
    }
    
    /**
     * Validate reminder time
     */
    public function validateRemindAt($attribute, $params)
    {
        $remindTime = strtotime($this->$attribute);
        $now = time();
        
        if ($remindTime <= $now) {
            $this->addError($attribute, 'Reminder time must be in the future');
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'created_by' => 'Created By',
            'description' => 'Description',
            'remind_at' => 'Remind At',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
    
    /**
     * Gets query for [[User]].
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    
    /**
     * Gets query for [[Creator]].
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
    
    /**
     * Get status list
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
    
    /**
     * Get status name
     */
    public function getStatusName()
    {
        return self::getStatusList()[$this->status] ?? 'Unknown';
    }
    
    /**
     * Mark reminder as completed
     */
    public function complete()
    {
        $this->status = self::STATUS_COMPLETED;
        return $this->save(false);
    }
    
    /**
     * Mark reminder as cancelled
     */
    public function cancel()
    {
        $this->status = self::STATUS_CANCELLED;
        return $this->save(false);
    }
    
    /**
     * Check if user can manage this reminder
     */
    public function canManage($userId)
    {
        return $this->created_by == $userId || $this->user_id == $userId;
    }
    
    /**
     * Check if reminder is pending
     */
    public function isPending()
    {
        return $this->status == self::STATUS_ACTIVE && strtotime($this->remind_at) <= time();
    }
    
    /**
     * Get pending reminders to send
     */
    public static function getPendingReminders($limit = 100)
    {
        $now = date('Y-m-d H:i:00');
        
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->andWhere(['<=', 'remind_at', $now])
            ->orderBy(['remind_at' => SORT_ASC])
            ->limit($limit)
            ->all();
    }
    
    /**
     * Get active reminders for user
     */
    public static function getActiveByUser($userId)
    {
        return static::find()
            ->where(['user_id' => $userId, 'status' => self::STATUS_ACTIVE])
            ->orderBy(['remind_at' => SORT_ASC])
            ->all();
    }
    
    /**
     * Override beforeSave
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_by = $this->created_by ?? Yii::$app->user->id;
            }
            return true;
        }
        return false;
    }
}