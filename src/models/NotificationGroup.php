<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;
use ZakharovAndrew\user\models\Notification;

class NotificationGroup extends ActiveRecord
{
    public static function tableName()
    {
        return 'notification_groups';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['position'], 'integer'],
        ];
    }

    public function getNotifications()
    {
        return $this->hasMany(Notification::class, ['notification_group_id' => 'id']);
    }
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                // Calculate the maximum position
                $maxPosition = self::find()->max('position');
                // Assign the new position
                $this->position = $maxPosition !== null ? $maxPosition + 1 : 1; // If maxPosition exists, increment it by 1; otherwise, set to 1
            }
            return true; // Proceed with saving
        }
        return false; // Do not save if parent beforeSave fails
    }
}