<?php

namespace ZakharovAndrew\user\models;

use Yii;

/**
 * This is the model class for table "user_settings_log".
 *
 * @property int $id
 * @property int $user_settings_id
 * @property int $changed_by
 * @property string|null $changed_at
 * @property string|null $old_value
 * @property string $new_value
 */
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