<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;
use ZakharovAndrew\user\models\Roles;
use ZakharovAndrew\user\models\NotificationGroup;

class Notification extends ActiveRecord
{
    public static function tableName()
    {
        return 'notifications';
    }

    public function rules()
    {
        return [
            [['group_id', 'name'], 'required'],
            [['group_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => NotificationGroup::class, 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    public function getGroup()
    {
        return $this->hasOne(NotificationGroup::class, ['id' => 'group_id']);
    }

    public function getUserSettings()
    {
        return $this->hasMany(UserNotificationSetting::class, ['notification_id' => 'id']);
    }

    // Новая связь с ролями
    public function getRoles()
    {
        return $this->hasMany(Roles::class, ['id' => 'role_id'])
            ->viaTable('notification_role', ['notification_id' => 'id'], ['role_id' => 'id']);
    }
}
