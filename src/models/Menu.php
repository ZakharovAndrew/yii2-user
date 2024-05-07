<?php

namespace ZakharovAndrew\user\models;

use Yii;
use ZakharovAndrew\user\models\User;

/**
 * Menu
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
                $actions = explode(',', $list[$controller_id]);
                
                //перебираем
                foreach ($params as $link => $title) {
                    
                    
                    $arr = explode('/', $link);
                    if ($list[$controller_id] == '*' || in_array(end($arr), $actions)) {
                        $items[] = ['label' => $title, 'url' => [$link]];
                    }
                }
            }
        }
        
        return $items;
    }
}