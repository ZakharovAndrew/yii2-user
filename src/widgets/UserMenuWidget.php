<?php

namespace ZakharovAndrew\user\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use ZakharovAndrew\user\Module;

class UserMenuWidget extends Widget
{
    public $user;
    public $guestContent;
    public $guestView = 'guest-menu';
    
    public function init()
    {
        parent::init();
        
        if ($this->user === null) {
            $this->user = Yii::$app->user->identity;
        }
        
        // Устанавливаем содержимое по умолчанию для гостей
        if ($this->guestContent === null) {
            $this->guestContent = $this->getDefaultGuestContent();
        }
    }
    
    public function run()
    {
        if (Yii::$app->user->isGuest) {
            if ($this->guestView) {
                return $this->render($this->guestView, [
                    'guestContent' => $this->guestContent,
                ]);
            }
            return $this->guestContent;
        }
        
        return $this->render('user-menu', [
            'user' => $this->user,
        ]);
    }
    
    protected function getDefaultGuestContent()
    {
        return Html::a(Module::t('Login'), ['/user/auth/login'], ['class' => 'nav-link']);
    }
}