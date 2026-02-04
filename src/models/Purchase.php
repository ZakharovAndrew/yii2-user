<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;
use ZakharovAndrew\user\Module;

/**
 * Purchase model
 * 
 * @property int $id
 * @property int $user_id
 * @property int $item_type
 * @property int $item_id
 * @property int $price
 * @property string $created_at
 * 
 * @property User $user
 * @property Achievement|null $achievement
 */
class Purchase extends ActiveRecord
{
    const TYPE_ACHIEVEMENT = 1;
    const TYPE_AVATAR = 2;
    const TYPE_WALLPAPER = 3;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchases';
    }
   
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'item_type', 'item_id', 'price'], 'required'],
            [['user_id', 'item_type', 'item_id', 'price'], 'integer'],
            ['price', 'compare', 'compareValue' => 0, 'operator' => '>=', 'message' => Module::t('Price must be greater than or equal to 0')],
            ['item_type', 'in', 'range' => array_keys(self::getItemTypeOptions())],
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
            'item_type' => Module::t('Item Type'),
            'item_id' => Module::t('Item ID'),
            'price' => Module::t('Price'),
            'created_at' => Module::t('Purchase Date'),
        ];
    }
    
    /**
     * Get item type options
     * 
     * @return array
     */
    public static function getItemTypeOptions()
    {
        return [
            self::TYPE_ACHIEVEMENT => Module::t('Achievement'),
            self::TYPE_AVATAR => Module::t('Avatar'),
            self::TYPE_WALLPAPER => Module::t('Wallpaper'),
        ];
    }
    
    /**
     * Get item type label
     * 
     * @return string
     */
    public function getItemTypeLabel()
    {
        $options = self::getItemTypeOptions();
        return isset($options[$this->item_type]) ? $options[$this->item_type] : Module::t('Unknown');
    }
    
    /**
     * Check if user already owns an item
     * 
     * @param int $user_id
     * @param int $item_type
     * @param int $item_id
     * @return bool
     */
    public static function userOwnsItem($user_id, $item_type, $item_id)
    {
        return self::find()
            ->where([
                'user_id' => $user_id,
                'item_type' => $item_type,
                'item_id' => $item_id,
            ])
            ->exists();
    }
    
    /**
     * Get user's total spent coins
     * 
     * @param int $user_id
     * @return int
     */
    public static function getUserTotalSpent($user_id)
    {
        return (int) self::find()
            ->where(['user_id' => $user_id])
            ->sum('price');
    }
    
    /**
     * Format purchase date for display
     * 
     * @return string
     */
    public function getFormattedDate()
    {
        return Yii::$app->formatter->asDatetime($this->created_at, 'short');
    }

}