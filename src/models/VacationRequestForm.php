<?php

namespace ZakharovAndrew\user\models;

use Yii;
use yii\base\Model;
use ZakharovAndrew\user\Module;

/**
 * Form for approving/rejecting vacation requests
 */
class VacationRequestForm extends Model
{
    public $vacation_id;
    public $action; // 'approve' or 'reject'
    public $comment;
    public $notify_user = true;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vacation_id', 'action'], 'required'],
            ['vacation_id', 'integer'],
            ['action', 'in', 'range' => ['approve', 'reject']],
            ['comment', 'string', 'max' => 500],
            ['comment', 'required', 'when' => function($model) {
                return $model->action === 'reject';
            }, 'message' => Module::t('Comment is required when rejecting vacation')],
            ['notify_user', 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'action' => Module::t('Action'),
            'comment' => Module::t('Comment'),
            'notify_user' => Module::t('Notify User'),
        ];
    }

    /**
     * Process vacation request
     */
    public function process()
    {
        if (!$this->validate()) {
            return false;
        }

        $vacation = Vacation::findOne($this->vacation_id);
        if (!$vacation) {
            $this->addError('vacation_id', Module::t('Vacation not found'));
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->action === 'approve') {
                $success = $vacation->approve(Yii::$app->user->id);
                $message = Module::t('Vacation approved successfully');
            } else {
                $success = $vacation->reject(Yii::$app->user->id, $this->comment);
                $message = Module::t('Vacation rejected successfully');
            }

            if (!$success) {
                $this->addError('action', Module::t('Error processing vacation request'));
                $transaction->rollBack();
                return false;
            }

            // Отправляем уведомление пользователю
            if ($this->notify_user) {
                $this->sendNotification($vacation);
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', $message);
            return true;

        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('action', Module::t('Error processing request: {error}', [
                'error' => $e->getMessage()
            ]));
            return false;
        }
    }

    /**
     * Send notification to user
     */
    protected function sendNotification($vacation)
    {
        $subject = $this->action === 'approve' 
            ? Module::t('Your vacation has been approved')
            : Module::t('Your vacation has been rejected');

        $message = $this->action === 'approve'
            ? $this->getApprovalMessage($vacation)
            : $this->getRejectionMessage($vacation);

        // Отправка email
        Yii::$app->mailer->compose()
            ->setTo($vacation->user->email)
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setSubject($subject)
            ->setHtmlBody($message)
            ->send();

        // In-app уведомление
        if (class_exists('app\models\Notification')) {
            $notification = new \app\models\Notification([
                'user_id' => $vacation->user_id,
                'type' => 'vacation_status',
                'title' => $subject,
                'message' => strip_tags($message),
                'related_model' => Vacation::class,
                'related_id' => $vacation->id,
            ]);
            $notification->save();
        }
    }

    /**
     * Get approval message template
     */
    protected function getApprovalMessage($vacation)
    {
        return Yii::$app->view->render('@vendor/zakharov-andrew/yii2-user/src/mail/vacation-approved', [
            'vacation' => $vacation,
            'approver' => Yii::$app->user->identity,
        ]);
    }

    /**
     * Get rejection message template
     */
    protected function getRejectionMessage($vacation)
    {
        return Yii::$app->view->render('@vendor/zakharov-andrew/yii2-user/src/mail/vacation-rejected', [
            'vacation' => $vacation,
            'approver' => Yii::$app->user->identity,
            'comment' => $this->comment,
        ]);
    }
}