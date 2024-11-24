<?php

/**
 * @copyright Copyright &copy; Andrey Zakharov, 2023 - 2024
 * @package yii2-user
 * @version 0.5.7
 */

namespace ZakharovAndrew\user\controllers;

use ZakharovAndrew\user\controllers\ParentController;
use yii\data\ActiveDataProvider;
use ZakharovAndrew\user\models\LoginAttempt;

/**
 * LoginAttemptController handles the viewing of login attempts.
 */
class LoginAttemptController extends ParentController
{
    /**
     * Lists all login attempts.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => LoginAttempt::find()->orderBy(['attempt_time' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}