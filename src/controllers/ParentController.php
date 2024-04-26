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
                            // if this action is always available
                            if (in_array($this->full_access_actions)) {
                                return true;
                            }

                            if (Yii::$app->user->isGuest) {
                                return false;
                            }
                            
                            return \ZakharovAndrew\user\models\User::isActionAllowed(Yii::$app->user->id, $this->controller_id, $action->id);
                        }
                    ],
                ],
            ],
        ];
    }

}
