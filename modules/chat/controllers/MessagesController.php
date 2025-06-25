<?php

namespace app\modules\chat\controllers;

use app\base\Controller;
use app\modules\chat\forms\MessageForm;
use app\models\User;
use Yii;
use yii\base\Action;
use yii\base\ExitException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `chat` module
 */
class MessagesController extends Controller
{
    /**
     * @var bool
     */
    public bool $prepareData = false;

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->getUser()->getIdentity()->isAdmin() || Yii::$app->getUser()->getIdentity(
                                )->chat_enable == 1;
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'delete' => ['post'],
                    'delete-conversation' => ['post'],
                    'read-conversation' => ['post'],
                    'upload-images' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param Action $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if ($action->id == 'index') {
            $this->prepareData = true;
        }

        return parent::beforeAction($action);
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }

    /**
     * @return bool
     * @throws ExitException
     */
    public function actionConversations(): bool
    {
        $user = $this->getCurrentUser();
        $query = $this->request->get('query');

        $conversations = $this->module->chatMessageManager->getConversations($user, $query);
        $contacts = $this->module->userManager->getContacts($user, $query);

        if ($conversations) {
            $conversations = array_values($conversations + $contacts);
        } else {
            $conversations = $contacts;
        }

        return $this->sendJson([
            'success' => true,
            'conversations' => $conversations,
            'newMessagesCounters' => $this->module->chatMessageManager->getNewMessagesCounters(
                $user->id
            ),
        ]);
    }

    /**
     * @param $contactId
     * @return bool
     * @throws ExitException
     * @throws NotFoundHttpException
     */
    public function actionMessages($contactId): bool
    {
        $contact = User::findOne($this->request->get('contactId'));

        if ($contact == null) {
            throw new NotFoundHttpException('Contact not found');
        }

        $messages = $this->module->chatMessageManager->getMessages(
            $contactId,
            $this->getCurrentUser()->id
        );

        return $this->sendJson([
            'success' => true,
            'contact' => [
                'id' => $contact->id,
                'username' => $contact->fullname,
                'full_name' => $contact->fullname,
                'avatar' => $contact->getAvatarUrl(48, 48),
                'url' => '',
                'online' => $contact->isOnline,
                'admin' => (bool)$contact->isAdmin(),
                'last_time_online' => $contact->last_login_time ? Yii::$app->formatter->asRelativeTime(
                    $contact->last_login_time
                ) : null,
            ],
            'messages' => $messages,

        ]);
    }

    /**
     * @return bool
     * @throws ExitException
     * @throws NotFoundHttpException
     */
    public function actionCreate(): bool
    {
        $contact = $this->getContactUser();
        $form = new MessageForm();
        $form->load($this->request->post());

        if ($form->validate()) {
            $message = $this->module->chatMessageManager->createMessage(
                $this->getCurrentUser()->id,
                $contact->id,
                $form->message
            );

            if (!$message->isNewRecord) {
                $message->refresh();
                return $this->sendJson([
                    'success' => true,
                    'message' => Yii::t('modules/chat', 'Message has been sent'),
                    'messageId' => $message->id,
                    'pendingMessageId' => (int)$this->request->post('pendingMessageId'),
                ]);
            } else {
                return $this->sendJson([
                    'success' => false,
                    'message' => Yii::t('modules/chat', $message->getErrorSummary(true)[0]),
                    'pendingMessageId' => (int)$this->request->post('pendingMessageId'),
                    'errors' => $form->errors,
                ]);
            }
        }

        return $this->sendJson([
            'success' => false,
            'message' => Yii::t('modules/chat', $form->getErrorSummary(true)[0]),
            'pendingMessageId' => (int)$this->request->post('pendingMessageId'),
            'errors' => $form->errors,
        ]);
    }

    /**
     * @return bool
     * @throws ExitException
     */
    public function actionDelete(): bool
    {
        $messageIds = $this->request->post('messages');
        $count = $this->module->chatMessageManager->deleteMessages(
            $this->getCurrentUser()->id,
            $messageIds
        );

        return $this->sendJson([
            'success' => true,
            'message' => Yii::t('modules/chat', 'Selected messages has been deleted'),
            'count' => $count,
        ]);
    }

    /**
     * @return bool
     * @throws ExitException
     * @throws NotFoundHttpException
     */
    public function actionDeleteConversation(): bool
    {
        $contact = $this->getContactUser(true);
        $success = $this->module->chatMessageManager->deleteConversation(
            $this->getCurrentUser()->id,
            $contact->id
        );

        return $this->sendJson([
            'success' => $success,
            'message' => Yii::t('modules/chat', 'Selected conversation has been deleted'),
        ]);
    }

    /**
     * @return bool
     * @throws ExitException
     * @throws NotFoundHttpException
     */
    public function actionReadConversation(): bool
    {
        $contact = $this->getContactUser();
        $success = $this->module->chatMessageManager->readConversation(
            $this->getCurrentUser()->id,
            $contact->id
        );

        return $this->sendJson([
            'success' => $success,
            'newMessagesCount' => $this->module->chatMessageManager->getNewMessagesCounters(
                $this->getCurrentUser()->id
            ),
            'message' => Yii::t('modules/chat', 'Updated'),
        ]);
    }

    /**
     * @return bool
     * @throws ExitException
     */
    public function actionNewMessagesCounters(): bool
    {
        return $this->sendJson(
            $this->module->chatMessageManager->getNewMessagesCounters($this->getCurrentUser()->id)
        );
    }

    /**
     * @return bool
     * @throws ExitException
     */
    public function actionNewMessagesCount(): bool
    {
        return $this->sendJson(
            $this->module->chatMessageManager->getNewMessagesCount($this->getCurrentUser()->id)
        );
    }

    /**
     * @param bool $includeBanned
     * @return User
     * @throws NotFoundHttpException
     */
    protected function getContactUser(bool $includeBanned = false): User
    {
        $userId = $this->request->getQueryParam('contactId');
        if ($userId == null) {
            $userId = $this->request->getBodyParam('contactId');
        }

        $user = User::findOne($userId);

        if ($user == null) {
            throw new NotFoundHttpException('Contact not found');
        }

        return $user;
    }
}
