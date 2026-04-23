<?php

namespace ZakharovAndrew\user\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use ZakharovAndrew\user\models\Friendship;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\Module;

/**
 * FriendController handles friend requests and friendships
 */
class FriendController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'send-request' => ['post'],
                    'accept' => ['post'],
                    'reject' => ['post'],
                    'cancel' => ['post'],
                    'remove' => ['post'],
                    'block' => ['post'],
                    'unblock' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * List of friends
     * 
     * @return string
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $friends = $user->getFriends()
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return $this->render('index', [
            'friends' => $friends,
            'friendsCount' => $user->friendsCount,
        ]);
    }

    /**
     * Friend requests page (pending, sent, rejected)
     * 
     * @return string
     */
    public function actionRequests($type = 'received')
    {
        $user = Yii::$app->user->identity;
        
        $data = [];
        
        switch ($type) {
            case 'received':
                // Полученные запросы (ожидающие ответа)
                $requests = $user->getPendingReceivedRequests()
                    ->with('user')
                    ->orderBy(['created_at' => SORT_DESC])
                    ->all();
                $data['title'] = Module::t('Friend Requests');
                break;
                
            case 'sent':
                // Отправленные запросы (ожидающие ответа)
                $requests = $user->getPendingSentRequests()
                    ->with('friend')
                    ->orderBy(['created_at' => SORT_DESC])
                    ->all();
                $data['title'] = Module::t('Sent Requests');
                break;
                
            case 'rejected':
                // Отклоненные запросы
                $requests = Friendship::find()
                    ->where(['status' => Friendship::STATUS_REJECTED])
                    ->andWhere([
                        'or',
                        ['user_id' => $user->id],
                        ['friend_id' => $user->id]
                    ])
                    ->with(['user', 'friend'])
                    ->orderBy(['responded_at' => SORT_DESC])
                    ->all();
                $data['title'] = Module::t('Rejected Requests');
                break;
                
            default:
                throw new NotFoundHttpException(Module::t('Page not found'));
        }
        
        $data['requests'] = $requests;
        $data['currentType'] = $type;
        
        return $this->render('requests', $data);
    }

    /**
     * Send friend request
     * 
     * @param int $id Friend ID
     * @return \yii\web\Response
     */
    public function actionSendRequest($id)
    {
        $currentUser = Yii::$app->user->identity;
        
        if ($currentUser->id == $id) {
            Yii::$app->session->setFlash('error', Module::t('You cannot add yourself as a friend'));
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
        
        $result = $currentUser->sendFriendRequest($id);
        
        if ($result instanceof Friendship) {
            Yii::$app->session->setFlash('success', Module::t('Friend request sent successfully'));
        } else {
            Yii::$app->session->setFlash('error', $result['error'] ?? Module::t('Failed to send friend request'));
        }
        
        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Accept friend request
     * 
     * @param int $id Friend ID (user who sent the request)
     * @return \yii\web\Response
     */
    public function actionAccept($id)
    {
        $currentUser = Yii::$app->user->identity;
        
        if ($currentUser->acceptFriendRequest($id)) {
            Yii::$app->session->setFlash('success', Module::t('Friend request accepted'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Failed to accept friend request'));
        }
        
        return $this->redirect(Yii::$app->request->referrer ?: ['requests']);
    }

    /**
     * Reject friend request
     * 
     * @param int $id Friend ID (user who sent the request)
     * @return \yii\web\Response
     */
    public function actionReject($id)
    {
        $currentUser = Yii::$app->user->identity;
        
        if ($currentUser->rejectFriendRequest($id)) {
            Yii::$app->session->setFlash('success', Module::t('Friend request rejected'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Failed to reject friend request'));
        }
        
        return $this->redirect(Yii::$app->request->referrer ?: ['requests']);
    }

    /**
     * Cancel sent friend request
     * 
     * @param int $id Friend ID (user who received the request)
     * @return \yii\web\Response
     */
    public function actionCancel($id)
    {
        $currentUser = Yii::$app->user->identity;
        $friendship = Friendship::find()
            ->where([
                'user_id' => $currentUser->id,
                'friend_id' => $id,
                'status' => Friendship::STATUS_PENDING
            ])
            ->one();
        
        if ($friendship && $friendship->cancel()) {
            Yii::$app->session->setFlash('success', Module::t('Friend request cancelled'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Failed to cancel friend request'));
        }
        
        return $this->redirect(Yii::$app->request->referrer ?: ['requests', 'type' => 'sent']);
    }

    /**
     * Remove friend
     * 
     * @param int $id Friend ID
     * @return \yii\web\Response
     */
    public function actionRemove($id)
    {
        $currentUser = Yii::$app->user->identity;
        
        if ($currentUser->removeFriend($id)) {
            Yii::$app->session->setFlash('success', Module::t('Friend removed successfully'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Failed to remove friend'));
        }
        
        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Block user
     * 
     * @param int $id User ID to block
     * @return \yii\web\Response
     */
    public function actionBlock($id)
    {
        $currentUser = Yii::$app->user->identity;
        
        if ($currentUser->id == $id) {
            Yii::$app->session->setFlash('error', Module::t('You cannot block yourself'));
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
        
        $friendship = Friendship::getFriendship($currentUser->id, $id);
        
        if (!$friendship) {
            // Create new friendship with blocked status
            $friendship = new Friendship();
            $friendship->user_id = $currentUser->id;
            $friendship->friend_id = $id;
        }
        
        if ($friendship->block()) {
            Yii::$app->session->setFlash('success', Module::t('User blocked successfully'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Failed to block user'));
        }
        
        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Unblock user
     * 
     * @param int $id User ID to unblock
     * @return \yii\web\Response
     */
    public function actionUnblock($id)
    {
        $currentUser = Yii::$app->user->identity;
        $friendship = Friendship::getFriendship($currentUser->id, $id);
        
        if ($friendship && $friendship->isBlocked()) {
            if ($friendship->delete()) {
                Yii::$app->session->setFlash('success', Module::t('User unblocked successfully'));
            } else {
                Yii::$app->session->setFlash('error', Module::t('Failed to unblock user'));
            }
        } else {
            Yii::$app->session->setFlash('error', Module::t('User is not blocked'));
        }
        
        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * Suggested friends
     * 
     * @return string
     */
    public function actionSuggestions()
    {
        $user = Yii::$app->user->identity;
        $suggestions = $user->getSuggestedFriends(20);
        
        return $this->render('suggestions', [
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * Search friends
     * 
     * @return string
     */
    public function actionSearch($q = '')
    {
        $user = Yii::$app->user->identity;
        
        // Исключаем уже друзей и самого пользователя
        $friendIds = $user->getFriends()->select('u.id')->column();
        $friendIds[] = $user->id;
        
        $query = User::find()
            ->where(['not in', 'id', $friendIds])
            ->andWhere(['!=', 'status', User::STATUS_DELETED]);
        
        if (!in_array($user->status, [User::STATUS_ADMIN, User::STATUS_SENIOR_ADMIN, User::STATUS_ROOT])) {
            $query->andWhere(['status' => $user->status]);
        }
        
        if ($user->status < User::STATUS_MANAGER) {
            return $this->render('search', [
                'users' => null,
                'query' => $q,
            ]);
        }
        
        if (!empty($q)) {
            $query->andWhere(['or',
                ['like', 'name', $q],
                ['like', 'username', $q],
                ['like', 'email', $q]
            ]);
        }
        
        $users = $query
            ->orderBy(['name' => SORT_ASC])
            ->all();
        
        return $this->render('search', [
            'users' => $users,
            'query' => $q,
        ]);
    }
}