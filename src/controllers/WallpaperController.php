<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use ZakharovAndrew\user\models\User;

class WallpaperController extends Controller
{
    public function actionIndex()
    {
        $wallpapers = Yii::$app->getModule('user')->wallpapers;
        $availableWallpapers = [];

        // Getting the roles of the current user
        $userRoles = User::getRolesByUserId(Yii::$app->user->id);

        foreach ($wallpapers as $wallpaper) {
            // Checking if the wallpaper is available for the user.
            if (array_intersect($userRoles, $wallpaper['roles'])) {
                $availableWallpapers[] = $wallpaper['url'];
            }
        }

        return $this->render('index', [
            'wallpapers' => $availableWallpapers,
        ]);
    }
}
