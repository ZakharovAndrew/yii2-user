<?php

namespace ZakharovAndrew\pages\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
// use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\Module;

/**
 * DefaultController implements the CRUD actions for User model.
 * @author Andrew Zakharov https://github.com/ZakharovAndrew
 */
class DefaultController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                       'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
}
