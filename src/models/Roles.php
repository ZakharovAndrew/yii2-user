<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\Module;
use \yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "roles".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $created_at
 * @property string|null $parameters
 */
class Roles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'roles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description'], 'string'],
            [['parameters', 'created_at'], 'safe'],
            [['title', 'code', 'function_to_get_all_subjects'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => Module::t('Title'),
            'description' => Module::t('Description'),
            'code' => Module::t('Code'),
            'parameters' => Module::t('Parameters'),
            'function_to_get_all_subjects' => Module::t('Function to get all role subjects'),
            'created_at' => 'Created At',
        ];
    }
    
    /**
     * Get all roles
     * 
     * @return array
     */
    public static function getRolesList()
    {
        return Yii::$app->cache->getOrSet('get_roles_list', function () {
            $arr = static::find()
                    ->select(['id', 'title'])
                    ->asArray()
                    ->all();

            return ArrayHelper::map($arr, 'id', 'title');
        }, 600);
    }
    
    /**
     * Get all user roles with available controller actions
     * 
     * @param integer $user_id User ID
     * @return mixed
     */
    public static function getRolesByUserId($user_id)
    {
        return Yii::$app->cache->getOrSet('get_roles_by_user_'.$user_id, function () use ($user_id) {
            return static::find()
                ->select('roles.*')
                ->leftJoin('user_roles', 'user_roles.role_id = roles.id')
                ->where(['user_roles.user_id' => $user_id])
                ->all();
        }, 60);
    }
    
    public static function getAllowedParametersList()
    {
        $items = [];
        $controllersAccessList = Yii::$app->getModule('user')->controllersAccessList;
        
        foreach ($controllersAccessList as $controller_id => $params) {
            if (is_string($controller_id)) {
                $submenu = [];
                // find submenu
                foreach ($params as $item_id => $item) {
                    if ($item_id != 'statuses') {
                        $submenu = array_merge($submenu, static::getSubItem($item_id, $item));
                    }
                }
                // don't create submenu
                if (count($submenu) == 1) {
                    $items = array_merge($items, $submenu);
                } else if (count($submenu) > 0) {
                    $items[] = ['label' => $controller_id, 'items' => $submenu];
                }
            } else {
                $items = array_merge($items, static::getSubItem($controller_id, $params));
            }
        }
        
        return $items;
    }
    
    public static function getSubItem($controller_id, $items)
    {
        $menu_items = [];     

        // Processing each menu item
        foreach ($items as $link => $item) {
            $url = $link;
            $label = $item;
            
            if (is_array($item) && is_int($link)) {
                $label = $item['label'];
            } else if (is_array($item)) {
                $menu_items[] = ['label' => $link, 'items' => static::getSubItem($controller_id, $item)];
                continue;
            }

            $menu_items[] = ['label' => $label, 'id' => $controller_id];
        }
        
        return $menu_items;
    }
    
    public function getParametersList()
    {
        return json_decode($this->parameters) ?? [];
    }
    
    public function roleSubject($subject)
    {
        if (!empty($this->function_to_get_all_subjects) && is_callable($this->function_to_get_all_subjects)) {
            // function name
            $func = $this->function_to_get_all_subjects;
            // exec function
            return $func()[$subject] ?? $subject;
        }
        
        return $subject;
    }
    
    public static function getRolesWithSubjects()
    {
        $list = [];
        
        $roles = static::find()->all();
        foreach ($roles as $role) {
            $list[$role->id] = $role->getSubjects();
        }
        
        return $list;
    }
    
    /**
     * Getting role subjects
     * @return array
     */
    public function getSubjects()
    {
        if (!empty($this->function_to_get_all_subjects) && is_callable($this->function_to_get_all_subjects)) {
            // function name
            $func = $this->function_to_get_all_subjects;
            // exec function
            return $func();
        }
        
        return [];
    }
    
    public function afterFind()
    {
        parent::afterFind();

        // If you need to parse JSON into an array during loading
        $this->parameters = ($this->parameters) ? Json::decode($this->parameters) : [];        
    }
    
    // before saving
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (is_array($this->parameters)) {
                $this->parameters = Json::encode($this->parameters);
            }
            return true;
        }
        return false;
    }
    
    /**
     * Reset cache after saving
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Yii::$app->cache->delete('get_roles_list');
    }
}
