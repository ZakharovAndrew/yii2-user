<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\models\User;

/**
 * Menu for navbar
 * *************
 *  
 * @link https://github.com/ZakharovAndrew/yii2-user/
 * @copyright Copyright (c) 2023-2024 Zakharov Andrew
 */
class Menu extends \yii\base\Model
{
    /**
     * Get menu for NavBar
     * @return array
     */
    public static function getNavBar()
    {
        $controllersAccessList = Yii::$app->getModule('user')->controllersAccessList;
        
        $list = User::getAccessList(Yii::$app->user->id);
       
        // menu items
        $items = [];
        
        foreach ($controllersAccessList as $controller_id => $params) {
            if (isset($list[$controller_id])) {
                $items = array_merge($items, static::getMenuItem($controller_id, $params, $list));
            }
        }
        
        return $items;
    }
    
    public static function getMenuItem($controller_id, $items, $list)
    {
        $menu_items = [];
        //перебираем
        foreach ($items as $link => $item) {
            if (is_array($item)) {
                $menu_items[] = ['label' => $link, 'items' => static::getMenuItem($controller_id, $item, $list)];
                continue;
            }

            $actions = explode(',', $list[$controller_id]);
            $arr = explode('/', $link);
            if ($list[$controller_id] == '*' || in_array(end($arr), $actions)) {
                $menu_items[] = ['label' => $item, 'url' => [$link]];
            }
            
        }
        
        return $menu_items;
    }
}
