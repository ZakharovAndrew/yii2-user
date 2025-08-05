<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\models\UserRoles;

/**
 * Menu for navbar
 * ***************
 *  
 * @link https://github.com/ZakharovAndrew/yii2-user/
 * @copyright Copyright (c) 2023-2025 Zakharov Andrew
 */
class Menu extends \yii\base\Model
{
    // 
    public static $accessList = null;
    
    /**
     * Get menu for NavBar
     * @return array
     */
    public static function getNavBar()
    {
        $controllersAccessList = Yii::$app->getModule('user')->controllersAccessList;
        
        static::$accessList = User::getAccessList(Yii::$app->user->id);
        //echo '<pre>';
        // menu items
        $items = [];
        
        foreach ($controllersAccessList as $controller_id => $params) {
            if (is_string($controller_id)) {
                $submenu = [];
                // find submenu
                foreach ($params as $item_id => $item) {
                    if (isset(static::$accessList[$item_id])) {
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
