<?php

namespace ZakharovAndrew\user\controllers;

use yii\web\Controller;

/**
 * Parental controller for access control.
 */
class ParentController extends Controller
{
    public $controller_id;
    public $full_access_actions = [];
    
    /**
     * {@inheritdoc}
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
                        'matchCallback' => function ($rule, $action) {
                            // need code
                        }
                    ],
                ],
            ],
        ];
    }

}
