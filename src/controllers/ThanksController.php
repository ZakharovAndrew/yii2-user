<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use ZakharovAndrew\user\models\Thanks;
use ZakharovAndrew\user\models\ThanksSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use ZakharovAndrew\user\Module;
use ZakharovAndrew\user\models\UserActivity;
use ZakharovAndrew\user\controllers\ParentController;

class ThanksController extends ParentController
{
    public $auth_access_actions = ['index', 'send', 'view'];
    
    public $action_allowed_roles = ['index' => ['admin']];

    /**
     * Lists all Thanks models.
     *
     * @return string
     */
    public function actionIndex()
    {        
        $searchModel = new ThanksSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Send thanks to user
     * 
     * @param int|null $id User ID to send thanks to
     * @return string|\yii\web\Response
     */
    public function actionSend($id = null)
    {
        $model = new Thanks();

        if ($model->load(Yii::$app->request->post())) {
            $model->author_id = Yii::$app->user->id;
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Module::t('Thanks sent'));
                return $this->redirect(['user/profile', 'id' => $model->user_id]);
            }
        }

        return $this->render('send', ['model' => $model, 'id' => $id]);
    }

    /**
     * View thanks for a user
     * 
     * @param int|null $id User ID, defaults to current user
     * @return string
     */
    public function actionView($id = null)
    {
        
        if (is_null($id)) {
            $id = Yii::$app->user->id;
        }
        
        $searchModel = new ThanksSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

        return $this->render('view', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'id' => $id,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Thanks::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('The requested page does not exist.'));
    }
    
    public function beforeAction($action)
    {
        // logging activity
        UserActivity::setActivity();
        
        return parent::beforeAction($action);
    }
}
