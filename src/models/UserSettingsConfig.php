<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\Module;

/**
 * This is the model class for table "user_settings_config".
 *
 * @property int $id
 * @property string $title
 * @property string $code
 * @property int|null $type
 * @property int|null $access_level
 * @property string|null $values
 */
class UserSettingsConfig extends \yii\db\ActiveRecord
{
    const CHANGE_USER_AND_ADMIN = 1;
    const CHANGE_USER_ONLY = 2;
    const CHANGE_ADMIN_ONLY = 3;
    const CHANGE_SYSTEM_ONLY = 4;
    
    const TYPE_INT = 1;
    const TYPE_STRING = 2;
    const TYPE_DATE = 3;
    const TYPE_TIME = 4;
    const TYPE_CHECKBOX = 5;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_settings_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'code'], 'required'],
            [['type', 'access_level'], 'integer'],
            [['values'], 'string'],
            [['title', 'code'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'    => 'ID',
            'title' => Module::t('Title'),
            'code'  => Module::t('Code'),
            'type'  => Module::t('Type'),
            'access_level' => Module::t('Who can change'),
            'values' => Module::t('Values'),
        ];
    }
    
    public static function getTypeOfSettings()
    {
        return [
            static::TYPE_INT => Module::t('Integer'),
            static::TYPE_STRING => Module::t('String'),
            static::TYPE_DATE => Module::t('Date'),
            static::TYPE_TIME => Module::t('Time'),
            static::TYPE_CHECKBOX => Module::t('Checkbox'),
        ];
    }
    
    public static function getAccessLevel()
    {               
        return [
            static::CHANGE_USER_AND_ADMIN => Module::t('User and Administrator'),
            static::CHANGE_USER_ONLY    => Module::t('User only'),
            static::CHANGE_ADMIN_ONLY   => Module::t('Administrator only'),
            static::CHANGE_SYSTEM_ONLY  => Module::t('System only'),
        ];
    }
    
    public function getUserSettingValue($user_id = null)
    {
        if (empty($user_id)) {
            $user_id = Yii::$app->user->id;
        }
        
        $model = UserSettings::find()
            ->select('values')
            ->where([
                'setting_config_id' => $this->id,
                'user_id' => $user_id
            ])->one();
        
        return $model->values ?? null;
    }
    
    public function getValues()
    {
        if (empty($this->values)) {
            return null;
        }
        
        $result = json_decode($this->values, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // JSON is valid
            return $result;
        }
        
        $arr =  explode("\r\n", $this->values);
        
        return array_combine($arr, $arr);
    }
}
