<?php

namespace ZakharovAndrew\user\models;

use Yii;

/**
 * This is the model class for table "user_settings".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $setting_config_id
 * @property string|null $values
 *
 * @property UserSettingsConfig $settingConfig
 */
class UserSettings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'setting_config_id'], 'integer'],
            [['values'], 'string', 'max' => 500],
            [['setting_config_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserSettingsConfig::class, 'targetAttribute' => ['setting_config_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'setting_config_id' => 'Setting Config ID',
            'values' => 'Values',
        ];
    }

    /**
     * Gets query for [[SettingConfig]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSettingConfig()
    {
        return $this->hasOne(UserSettingsConfig::class, ['id' => 'setting_config_id']);
    }
    
    /**
     * Saving a setting value
     * @param int $user_id
     * @param int $setting_config_id
     * @param string $values
     */
    public static function saveValue($user_id, $setting_config_id, $values)
    {
        $params = [
            'user_id' => $user_id,
            'setting_config_id' => $setting_config_id
        ];
        
        // maybe the setting already exists
        $model = static::find()->where($params)->one();
        
        if (!$model) {
            $model = new UserSettings($params);
        }
        
        // Logging the old value before the change
        $oldValue = $model ? $model->values : null;
        
        // change value
        $model->values = $values;
        $model->save();
        
        // Do not log if the value has not changed
        if ($oldValue == $values) {
            return true;
        }
        
        // Save log
        $log = new UserSettingsLog();
        $log->user_settings_id = $model->id;
        $log->changed_by = Yii::$app->user->id; // ID current user
        $log->old_value = $oldValue;
        $log->new_value = $values;
        $log->save();
    }
    
    /**
     * Gets the change log for the setting.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChangeLog()
    {
        return $this->hasMany(UserSettingsLog::class, ['user_settings_id' => 'id']);
    }
    
    
    /**
     * Reset cache after saving
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->cache->delete('get_users_settings_'.$this->user_id.'_'.$this->setting_config_id);
    }
}
