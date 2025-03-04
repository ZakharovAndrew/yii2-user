<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\db\ActiveRecord;
use ZakharovAndrew\user\models\Roles;
use ZakharovAndrew\user\models\NotificationGroup;
use ZakharovAndrew\user\Module;

/**
 * This is the model class for table "notifications".
 * It represents a notification and its associated roles, group, and settings.
 */
class Notification extends ActiveRecord
{
    public static function tableName()
    {
        return 'notifications';
    }

    public function rules()
    {
        return [
            [['notification_group_id', 'name'], 'required'],
            [['notification_group_id'], 'integer'],
            [['name', 'code_name', 'function_to_call'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['roles'], 'safe'],
            [['notification_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => NotificationGroup::class, 'targetAttribute' => ['notification_group_id' => 'id']],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Module::t('Name'),
            'description' => Module::t('Description'),
            'code_name' => Module::t('Code Name'),
            'roles' => Module::t('Roles'),
            'function_to_call' => Module::t('Function to call'),
        ];
    }
    
    /**
     * This method defines the relationship between a notification and its group.
     * 
     * @return \yii\db\ActiveQuery the relation to the NotificationGroup model.
     */
    public function getGroup()
    {
        return $this->hasOne(NotificationGroup::class, ['id' => 'notification_group_id']); // One-to-one relationship with NotificationGroup.
    }


    public function getUserSetting($userId)
    {
        return UserNotificationSetting::findOne(['notification_id' => $this->id, 'user_id' => $userId]);
    }

    /**
     * This method defines the many-to-many relationship between notifications and roles via the 'notification_role' junction table.
     * 
     * @return \yii\db\ActiveQuery the relation to the Roles model.
     */
    public function getRoles()
    {
        return $this->hasMany(Roles::class, ['id' => 'role_id'])
            ->viaTable('notification_role', ['notification_id' => 'id']);
    }

    /**
     * This method retrieves the IDs of all roles linked to this notification.
     * 
     * @return array the list of role IDs associated with this notification.
     */
    public function getRoleIds()
    {
        return $this->getRoles()->select('id')->column(); // Retrieve the IDs of all related roles.
    }

    /**
     * Sets the roles for this notification.
     *
     * @param array $roleIds The array of role IDs to associate with this notification.
     */
    public function setRoles($roleIds)
    {
        $currentRoleIds = $this->getRoleIds(); // Получаем текущие ID ролей

        if (is_array($roleIds)) {
            $rolesToAdd = array_diff($roleIds, $currentRoleIds);
            $rolesToRemove = array_diff($currentRoleIds, $roleIds);
        } else {
            $rolesToAdd = [];
            $rolesToRemove = $currentRoleIds ?? [];
        }
        
        foreach ($rolesToRemove as $roleId) {
            $this->unlink('roles', Roles::findOne($roleId), true);
        }
                
        // add new roles
        foreach ($rolesToAdd as $roleId) {
            $this->link('roles', Roles::findOne($roleId));
        }
    }

    /**
     * Clears all roles associated with this notification.
     */
    public function clearRoles()
    {
        $this->unlinkAll('roles', true); // Unlink all roles from the notification.
    }
}
