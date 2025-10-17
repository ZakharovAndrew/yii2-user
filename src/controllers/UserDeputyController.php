<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\models\UserSearch;
use ZakharovAndrew\user\models\UserDeputy;
use ZakharovAndrew\user\Module;

/**
 * UserDeputyController implements the CRUD actions for UserDeputy model.
 */
class UserDeputyController extends ParentController
{
    public $controller_id = 1008;
            
    /**
     * Lists all deputies for a user.
     * 
     * @param int $user_id
     * @return string
     */
    public function actionIndex($user_id = null, $mode = 'active')
    {
        // Если user_id не указан, используем текущего пользователя
        if ($user_id === null) {
            $user_id = Yii::$app->user->id;
        }

        $user = $this->findUserModel($user_id);
        
        // Проверяем права доступа
        if (!$this->canManageDeputies($user)) {
            Yii::$app->session->setFlash('error', Module::t('You do not have permission to manage deputies for this user.'));
            return $this->redirect(['/user/user/profile']);
        }

        if ($mode == 'all') {
            $deputies = $user->getDeputies()->orderBy('valid_from DESC')->with('deputyUser')->all();
        } else {
            $deputies = $user->getActiveDeputies()->with('deputyUser')->all();
        }
        
        $availableUsers = User::getAvailableUsersForDeputy($user_id);

        return $this->render('index', [
            'user' => $user,
            'deputies' => $deputies,
            'availableUsers' => $availableUsers,
            'mode' => $mode
        ]);
    }
    
    /**
     * Lists all users with deputies information.
     * 
     * @return string
     */
    public function actionList()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        /*$query = User::find()
            ->where(['!=', 'status', User::STATUS_DELETED]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);*/

        // Preload deputies for all users to avoid N+1 query problem
        //$users = $dataProvider->getModels();
        //$this->preloadDeputies($users);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Preload deputies for users to optimize queries
     * 
     * @param User[] $users
     */
    protected function preloadDeputies($users)
    {
        $userIds = array_map(function($user) {
            return $user->id;
        }, $users);

        // Get all active deputies for these users
        $deputies = UserDeputy::find()
            ->with('deputyUser')
            ->where(['user_id' => $userIds])
            ->andWhere(['is_active' => UserDeputy::STATUS_ACTIVE])
            ->andWhere(['<=', 'valid_from', date('Y-m-d H:i:s')])
            ->andWhere(['or', ['>=', 'valid_to', date('Y-m-d H:i:s')], ['valid_to' => null]])
            ->orderBy(['valid_from' => SORT_ASC])
            ->all();

        // Group deputies by user_id
        $deputiesByUser = [];
        foreach ($deputies as $deputy) {
            if (!isset($deputiesByUser[$deputy->user_id])) {
                $deputiesByUser[$deputy->user_id] = [];
            }
            $deputiesByUser[$deputy->user_id][] = $deputy;
        }

        // Assign deputies to users
        foreach ($users as $user) {
            $user->setDeputies(isset($deputiesByUser[$user->id]) ? $deputiesByUser[$user->id] : []);
        }
    }

    /**
     * Creates a new deputy relationship.
     * 
     * @param int $user_id
     * @return string|\yii\web\Response
     */
    public function actionCreate($user_id)
    {
        $user = $this->findUserModel($user_id);
        
        // Проверяем права доступа
        if (!$this->canManageDeputies($user)) {
            Yii::$app->session->setFlash('error', Module::t('You do not have permission to manage deputies for this user.'));
            return $this->redirect(['index', 'user_id' => $user_id]);
        }

        $model = new UserDeputy();
        $model->user_id = $user_id;
        $model->created_by = Yii::$app->user->id;
        $model->is_active = UserDeputy::STATUS_ACTIVE;

        if ($model->load(Yii::$app->request->post())) {
            // Устанавливаем время для дат
            if (!empty($model->valid_from)) {
                $model->valid_from = date('Y-m-d 00:00:00', strtotime($model->valid_from));
            }
            if (!empty($model->valid_to)) {
                $model->valid_to = date('Y-m-d 23:59:59', strtotime($model->valid_to));
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', Module::t('Deputy successfully added.'));
                return $this->redirect(['index', 'user_id' => $user_id]);
            } else {
                Yii::$app->session->setFlash('error', Module::t('Error adding deputy. Please check the input data.'));
            }
        }

        $availableUsers = User::getAvailableUsersForDeputy($user_id);

        return $this->render('create', [
            'model' => $model,
            'user' => $user,
            'availableUsers' => $availableUsers,
        ]);
    }

    /**
     * Updates an existing deputy relationship.
     * 
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $user = $model->user;
        
        // Проверяем права доступа
        if (!$this->canManageDeputies($user)) {
            Yii::$app->session->setFlash('error', Module::t('You do not have permission to manage this deputy.'));
            return $this->redirect(['index', 'user_id' => $user->id]);
        }

        if ($model->load(Yii::$app->request->post())) {
            // Устанавливаем время для дат
            if (!empty($model->valid_from)) {
                $model->valid_from = date('Y-m-d 00:00:00', strtotime($model->valid_from));
            }
            if (!empty($model->valid_to)) {
                $model->valid_to = date('Y-m-d 23:59:59', strtotime($model->valid_to));
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', Module::t('Deputy successfully updated.'));
                return $this->redirect(['index', 'user_id' => $user->id]);
            } else {
                Yii::$app->session->setFlash('error', Module::t('Error updating deputy. Please check the input data.'));
            }
        }

        // Форматируем даты для отображения в форме
        if ($model->valid_from) {
            $model->valid_from = date('Y-m-d', strtotime($model->valid_from));
        }
        if ($model->valid_to) {
            $model->valid_to = date('Y-m-d', strtotime($model->valid_to));
        }

        return $this->render('update', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    /**
     * Deactivates a deputy relationship.
     * 
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionRemove($id)
    {
        $model = $this->findModel($id);
        $user = $model->user;
        
        // Проверяем права доступа
        if (!$this->canManageDeputies($user)) {
            Yii::$app->session->setFlash('error', Module::t('You do not have permission to remove this deputy.'));
            return $this->redirect(['index', 'user_id' => $user->id]);
        }

        $model->is_active = UserDeputy::STATUS_INACTIVE;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', Module::t('Deputy successfully removed.'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Error removing deputy.'));
        }

        return $this->redirect(['index', 'user_id' => $user->id]);
    }

    /**
     * Shows deputies for current user.
     * 
     * @return string
     */
    public function actionMyDeputies()
    {
        $user_id = Yii::$app->user->id;
        $user = $this->findUserModel($user_id);

        $deputies = $user->getActiveDeputies()->with('deputyUser')->all();
        $deputyForUsers = $user->getCurrentDeputyForUsers();

        return $this->render('my-deputies', [
            'user' => $user,
            'deputies' => $deputies,
            'deputyForUsers' => $deputyForUsers,
        ]);
    }

    /**
     * Quick add deputy via AJAX.
     * 
     * @param int $user_id
     * @param int $deputy_user_id
     * @return \yii\web\Response
     */
    public function actionQuickAdd($user_id, $deputy_user_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $user = $this->findUserModel($user_id);
        
        // Проверяем права доступа
        if (!$this->canManageDeputies($user)) {
            return ['success' => false, 'message' => Module::t('You do not have permission to manage deputies for this user.')];
        }

        // Проверяем, не является ли deputy сам себе
        if ($user_id == $deputy_user_id) {
            return ['success' => false, 'message' => Module::t('User cannot be a deputy for themselves.')];
        }

        // Проверяем, не добавлен ли уже этот deputy
        if ($user->hasDeputy($deputy_user_id)) {
            return ['success' => false, 'message' => Module::t('This user is already a deputy.')];
        }

        // Добавляем deputy с текущей датой и без даты окончания
        if ($user->addDeputy($deputy_user_id, date('Y-m-d'), null, Yii::$app->user->id)) {
            return ['success' => true, 'message' => Module::t('Deputy successfully added.')];
        } else {
            return ['success' => false, 'message' => Module::t('Error adding deputy.')];
        }
    }

    /**
     * Finds the UserDeputy model based on its primary key value.
     * 
     * @param int $id
     * @return UserDeputy
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = UserDeputy::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('The requested deputy does not exist.'));
    }

    /**
     * Finds the User model based on its primary key value.
     * 
     * @param int $id
     * @return User
     * @throws NotFoundHttpException
     */
    protected function findUserModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('The requested user does not exist.'));
    }

    /**
     * Checks if current user can manage deputies for the given user.
     * 
     * @param User $user
     * @return bool
     */
    protected function canManageDeputies($user)
    {
        // Пользователь может управлять своими заместителями
        if ($user->id == Yii::$app->user->id) {
            return true;
        }

        // Администраторы могут управлять заместителями всех пользователей
        if (Yii::$app->user->identity->isAdmin()) {
            return true;
        }

        // Менеджеры могут управлять заместителями пользователей с меньшим статусом
        $currentUser = Yii::$app->user->identity;
        if ($currentUser && $user->status < $currentUser->status) {
            return true;
        }

        return false;
    }
}