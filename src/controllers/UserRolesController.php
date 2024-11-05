<?php

namespace ZakharovAndrew\user\controllers;

use ZakharovAndrew\user\models\UserRoles;
use ZakharovAndrew\user\models\UserRolesSearch;
use ZakharovAndrew\user\Module;
use yii\web\NotFoundHttpException;
use Yii;
use yii\helpers\Url;

/**
 * UserRolesController implements the CRUD actions for UserRoles model.
 * @author Andrew Zakharov https://github.com/ZakharovAndrew
 */
class UserRolesController extends ParentController
{
    public $controller_id = 1003;
    
    /**
     * Lists all UserRoles models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserRolesSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new UserRoles model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($user_id)
    {
        $model = new UserRoles();
        $model->user_id = $user_id;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->cache->delete('get_users_roles_'.$model->user_id);
                Yii::$app->session->setFlash('success', Module::t('Role added'));
                return $this->redirect(Url::previous('user_index') ?? ['/user/user/index']);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserRoles model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        Yii::$app->cache->delete('get_users_roles_'.$model->user_id);
        
        $model->delete();

        return $this->redirect(Url::previous('user_index') ?? ['/user/user/index']);
    }

    /**
     * Finds the UserRoles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return UserRoles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserRoles::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
