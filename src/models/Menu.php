<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\models\UserRoles;

/**
 * Dynamic menu builder for navbar based on user permissions and roles
 *  
 * @author Zakharov Andrew
 * @link https://github.com/ZakharovAndrew/yii2-user/
 * @copyright Copyright (c) 2023-2026 Zakharov Andrew
 */
class Menu extends \yii\base\Model
{
    /**
     * @var array|null Cached user access list
     */
    public static $accessList = null;
    
    /**
     * Get menu items for NavBar based on user permissions
     * 
     * @return array Array of menu items compatible with NavBar widget
     * @throws InvalidConfigException If user module is not configured properly
     */
    public static function getNavBar()
    {
        $module = Yii::$app->getModule('user');
        
        if (!$module || !isset($module->controllersAccessList)) {
            throw new InvalidConfigException('User module is not configured correctly');
        }
        
        $controllersAccessList = $module->controllersAccessList;
        
        static::initializeAccessList();

        return static::buildMenuItems($controllersAccessList);
    }
    
    /**
     * Initialize user access list
     */
    private static function initializeAccessList()
    {
        if (static::$accessList === null) {
            static::$accessList = User::getAccessList(Yii::$app->user->id) ?? [];
        }
    }
    
    /**
     * Build menu items recursively
     * 
     * @param array $controllersAccessList Access configuration for controllers
     * @return array Processed menu items
     */
    private static function buildMenuItems(array $controllersAccessList)
    {
        $items = [];
        
        foreach ($controllersAccessList as $controller_id => $params) {
            if (is_string($controller_id)) {
                // checking for the status required to display the menu item
                if (isset($params['statuses']) && !static::checkStatus($params['statuses'])) {
                    continue;
                }
                
                $submenu = [];
                // find submenu
                foreach ($params as $item_id => $item) {
                    if ($item_id != 'statuses' && isset(static::$accessList[$item_id])) {
                        //$submenu[$item_id] = $item;
                        $submenu = array_merge($submenu, static::getMenuItem($item_id, $item));
                        //$items = array_merge($items, static::getMenuItem($item_id, $item));
                    }
                }
                // don't create submenu
                if (count($submenu) == 1) {
                    $items = array_merge($items, $submenu);
                } else if (count($submenu) > 0) {
                    $items[] = ['label' => $controller_id, 'items' => $submenu];
                }
            } else if (isset(static::$accessList[$controller_id])) {
                $items = array_merge($items, static::getMenuItem($controller_id, $params));
            }
        }

        return $items;
    }
    
    /**
     * Process a menu category
     * 
     * @param string $categoryName Category name/label
     * @param array $categoryConfig Category configuration
     * @return array Processed menu items for this category
     */
    private static function processCategory(string $categoryName, array $categoryConfig)
    {
        // Check status requirement for the whole category
        if (isset($categoryConfig['statuses']) && !self::checkStatus($categoryConfig['statuses'])) {
            return [];
        }
        
        $submenu = [];
        
        foreach ($categoryConfig as $itemId => $itemConfig) {
            if ($itemId === 'statuses') {
                continue;
            }
            
            if (isset(self::$accessList[$itemId])) {
                $menuItems = self::getMenuItem($itemId, $itemConfig);
                $submenu = array_merge($submenu, $menuItems);
            }
        }
        
        return self::formatCategoryOutput($categoryName, $submenu);
    }
    
    /**
     * Format category output based on submenu count
     * 
     * @param string $categoryName
     * @param array $submenu
     * @return array
     */
    private static function formatCategoryOutput(string $categoryName, array $submenu)
    {
        $submenuCount = count($submenu);
        
        if ($submenuCount === 0) {
            return [];
        }
        
        if ($submenuCount === 1) {
            return $submenu;
        }
        
        return [['label' => $categoryName, 'items' => $submenu]];
    }
    
    /**
     * Getting menu items
     * 
     * @param int $controller_id
     * @param array $items
     * @return array
     */
    public static function getMenuItem($controller_id, $items)
    {
        $menu_items = [];
        $actions = explode(',', static::$accessList[$controller_id]);
        

        // Processing each menu item
        foreach ($items as $link => $item) {
            
            $url = $link;
            $label = $item;
            
            if (is_array($item) && is_int($link) && isset($item['url'])) {
                // checking for the role required to display the menu item
                if (isset($item['roles']) && !static::checkRoles($item['roles'])) {
                    continue;
                }
                
                // checking for the status required to display the menu item
                if (isset($item['statuses']) && !static::checkStatus($item['statuses'])) {
                    continue;
                }
                
                $url = $item['url'];
                $label = $item['label'];
            } else if (is_array($item)) {
                $menu_items[] = ['label' => $link, 'items' => static::getMenuItem($controller_id, $item)];
                continue;
            }

            $arr = explode('/', $link);
            if (static::$accessList[$controller_id] == '*' || in_array(end($arr), $actions)) {
                $menu_items[] = ['label' => $label, 'url' => [$url]];
            }
            
        }
        
        return $menu_items;
    }
    
    /**
     * Checking for the role required to display the menu item
     * @param string|array $roles
     * @return boolean
     */
    public static function checkRoles($roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        
        $userRoles = UserRoles::getUserRoles(Yii::$app->user->id);
        foreach ($userRoles as $role) {
            if (in_array($role['code'], $roles)) {
                return true;
            }
        }
        
        return false;
    }
    
    public static function checkStatus($statuses)
    {
        if (is_string($statuses) || is_numeric($statuses)) {
            $statuses = [$statuses];
        }
        
        return in_array(Yii::$app->user->identity->status, $statuses);
    }
}
