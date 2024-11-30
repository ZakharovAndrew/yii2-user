<?php

namespace ZakharovAndrew\user\models;

use Yii;

class UserSettingsLog extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'user_settings_log';
    }

    public function rules()
    {
        return [
            [['user_settings_id', 'changed_by', 'new_value'], 'required'],
            [['user_settings_id', 'changed_by'], 'integer'],
            [['changed_at'], 'safe'],
            [['old_value', 'new_value'], 'string', 'max' => 500],
        ];
    }
}