<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use ZakharovAndrew\user\models\UserActivity;

/**
 * Parental controller for access control.
 * @author Andrew Zakharov https://github.com/ZakharovAndrew
 */
class ParentController extends Controller
{
    public $controller_id;
    
    /**
     * Actions that are available to any user
     * @var array 
     */
    public $full_access_actions = [];
    
    /**
     * Actions that require authorization
     * @var array 
     */
    public $auth_access_actions = [];
    
    /**
     * Actions and the roles for which they are available
     * @var array 
     */
    public $action_allowed_roles = [];
    
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
                        'roles' => ['?', '@'],
                        'matchCallback' => function ($rule, $action) {
                            // if this action is always available
                            if (in_array($action->id, $this->full_access_actions)) {
                                return true;
                            }
                            
                            //if the action is in the list of allowed authorized users
                            if (in_array($action->id, $this->auth_access_actions) && !Yii::$app->user->isGuest) {
                                return true;
                            }

                            if (Yii::$app->user->isGuest) {
                                return false;
                            }
                            
                            // Does the user have a role that is required to access this action?
                            if (isset($this->action_allowed_roles[$action->id]) && !Yii::$app->user->identity->hasRole($this->action_allowed_roles[$action->id])) {
                                return false;
                            }
                            
                            return \ZakharovAndrew\user\models\User::isActionAllowed(Yii::$app->user->id, $this->controller_id, $action->id);
                        }
                    ],
                ],
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // Логирования начала и конца активности
        UserActivity::setActivity();
        
        return parent::beforeAction($action);
    }

}
