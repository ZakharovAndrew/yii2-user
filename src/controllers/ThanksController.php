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

class ThanksController extends Controller
{
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

    /**
     * Lists all Thanks models.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->identity->hasRole('admin')) {
            throw new NotFoundHttpException(Module::t('The requested page does not exist.'));
        }
        
        $searchModel = new ThanksSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

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
