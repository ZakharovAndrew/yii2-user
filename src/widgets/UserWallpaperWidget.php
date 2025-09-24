<?php

namespace ZakharovAndrew\user\widgets;

use Yii;
use yii\base\Widget;
use ZakharovAndrew\user\models\User;

class UserWallpaperWidget extends Widget
{
    public $user;
    
    public function init()
    {
        parent::init();
        
        if ($this->user === null && !Yii::$app->user->isGuest) {
            $this->user = Yii::$app->user->identity;
        }
    }
    
    public function run()
    {
        if (Yii::$app->user->isGuest || !$this->user) {
            return '';
        }
        
        $wallpaper = $this->user->getWallpaper();
        
        return $this->render('user-wallpaper', [
            'user' => $this->user,
            'wallpaper' => $wallpaper,
        ]);
    }
}