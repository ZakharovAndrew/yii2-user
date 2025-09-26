<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use ZakharovAndrew\user\Module;

/**
 * This is the model class for table "user_wallpapers".
 * Represents wallpaper entity with positioning, styling and access control
 *
 * @property int $id
 * @property string $name Wallpaper name
 * @property string $image_url URL to wallpaper image
 * @property string $css_settings CSS properties for desktop devices
 * @property string $mobile_css_settings CSS properties for mobile devices
 * @property int $position Sorting position (lower numbers appear first)
 * @property int $status Activity status (active/inactive)
 * @property string $roles Comma-separated list of role codes that can use this wallpaper
 * @property string $created_at
 * @property string $updated_at
 */
class Wallpaper extends ActiveRecord
{
    /** Active status constant */
    const STATUS_ACTIVE = 1;
    /** Inactive status constant */
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     * @return string table name
     */
    public static function tableName()
    {
        return 'user_wallpapers';
    }

    /**
     * {@inheritdoc}
     * @return array validation rules
     */
    public function rules()
    {
        return [
            // Required fields
            [['name', 'image_url'], 'required'],
            
            [['css_settings', 'mobile_css_settings', 'roles'], 'string'],
            [['position', 'status'], 'integer'],
            
            // Date fields
            [['created_at', 'updated_at'], 'safe'],
            
            [['name'], 'string', 'max' => 255],
            [['image_url'], 'string', 'max' => 500],
            
            // Default values
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['position', 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     * @return array attribute labels for form display
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Module::t('Name'),
            'image_url' => Module::t('Image URL'),
            'css_settings' => Module::t('CSS Settings'),
            'mobile_css_settings' => Module::t('Mobile CSS Settings'),
            'position' => Module::t('Position'),
            'status' => Module::t('Status'),
            'roles' => Module::t('Roles'),
            'created_at' => Module::t('Creation Date'),
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get list of status options for dropdown
     * @return array status options
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => Module::t('Active'),
            self::STATUS_INACTIVE => Module::t('Inactive'),
        ];
    }

    /**
     * Check if wallpaper is available for specific user role
     * @param string $roleCode Role code to check
     * @return bool true if available for the role
     */
    public function isAvailableForRole($roleCode)
    {
        // Available for all roles if no restrictions set
        if (empty($this->roles)) {
            return true;
        }

        // Convert comma-separated string to array and check if role exists
        $allowedRoles = array_map('trim', explode(',', $this->roles));
        return in_array($roleCode, $allowedRoles);
    }

    /**
     * Get appropriate CSS settings for current device type
     * @return string CSS settings
     */
    public function getCssForDevice()
    {
        // Check if mobile device detected and mobile settings exist
        $isMobile = Yii::$app->params['isMobile'] ?? false;
        
        if ($isMobile && !empty($this->mobile_css_settings)) {
            return $this->mobile_css_settings;
        }
        
        // Fall back to desktop CSS settings
        return $this->css_settings;
    }

    /**
     * Get all active wallpapers ordered by position
     * @return Wallpaper[] array of active wallpapers
     */
    public static function getActiveWallpapers()
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->orderBy(['position' => SORT_ASC])
            ->all();
    }

    /**
     * Get wallpapers available for specific user based on their roles
     * @param User $user User model instance
     * @return Wallpaper[] array of available wallpapers
     */
    public static function getAvailableWallpapersForUser($user)
    {
        return Yii::$app->cache->getOrSet('wallpaper_list'.$user->id, function () use ($user) {
            // Get all roles assigned to the user
            $userRoles = ArrayHelper::getColumn(Roles::getRolesByUserId($user->id), 'code');

            $wallpapers = static::find()
                ->where(['status' => self::STATUS_ACTIVE])
                ->orderBy(['position' => SORT_ASC])
                ->all();

            $result = [];
            foreach ($wallpapers as $wallpaper) {
                if (empty($wallpaper->roles)) {
                    $result[] = $wallpaper;
                    continue;
                }
                foreach ($userRoles as $role) {
                    if ($wallpaper->isAvailableForRole($role)) {
                        $result[] = $wallpaper;
                        continue;
                    }
                }
            }

            return $result;
        }, 600);
    }

    /**
     * Before save event handler
     * @param bool $insert whether this is a new record
     * @return bool whether to continue saving
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {            
            if (empty($this->position)) {
                $maxPosition = self::find()->max('position');
                $this->position = $maxPosition ? $maxPosition + 1 : 1;
            }
            
            $this->updated_at = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    /**
     * Get formatted CSS settings as HTML style attribute
     * @param bool $forMobile whether to get mobile settings
     * @return string formatted style attribute
     */
    public function getFormattedCss($forMobile = false)
    {
        $css = $forMobile ? $this->mobile_css_settings : $this->css_settings;
        
        if (empty($css)) {
            return '';
        }
        
        // Convert JSON or raw CSS to style attribute format
        if (strpos($css, '{') !== false) {
            // Assume JSON format: {"background-size": "cover", "opacity": "0.8"}
            $decoded = json_decode($css, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $styles = [];
                foreach ($decoded as $property => $value) {
                    $styles[] = "{$property}: {$value}";
                }
                return implode('; ', $styles) . ';';
            }
        }
        
        // Assume raw CSS format: background-size: cover; opacity: 0.8;
        return $css;
    }

    /**
     * Get array of allowed roles for this wallpaper
     * @return array list of role codes
     */
    public function getAllowedRolesArray()
    {
        if (empty($this->roles)) {
            return [];
        }
        
        return array_map('trim', explode(',', $this->roles));
    }

    /**
     * Set allowed roles from array
     * @param array $roles array of role codes
     */
    public function setAllowedRolesArray($roles)
    {
        $this->roles = implode(',', array_map('trim', $roles));
    }
    
    /**
     * Move wallpaper position up
     * @return bool
     */
    public function moveUp()
    {
        $previous = self::find()
            ->where(['<', 'position', $this->position])
            ->orderBy(['position' => SORT_DESC])
            ->one();
            
        if ($previous) {
            $tempPosition = $this->position;
            $this->position = $previous->position;
            $previous->position = $tempPosition;
            
            return $this->save(false) && $previous->save(false);
        }
        
        return false;
    }

    /**
     * Move wallpaper position down
     * @return bool
     */
    public function moveDown()
    {
        $next = self::find()
            ->where(['>', 'position', $this->position])
            ->orderBy(['position' => SORT_ASC])
            ->one();
            
        if ($next) {
            $tempPosition = $this->position;
            $this->position = $next->position;
            $next->position = $tempPosition;
            
            return $this->save(false) && $next->save(false);
        }
        
        return false;
    }
}
