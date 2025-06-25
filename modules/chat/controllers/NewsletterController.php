<?php

declare(strict_types=1);

namespace app\modules\chat\controllers;

use app\base\Controller;
use app\models\User;
use app\modules\chat\forms\NewsLetterForm;
use Yii;
use yii\base\ExitException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class NewsletterController extends Controller
{
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
                            return Yii::$app->getUser()->getIdentity()->isAdmin();
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'send' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return bool
     * @throws ExitException
     */
    public function actionSend(): bool
    {
        $form = new NewsLetterForm();
        $form->load($this->request->post());

        if ($form->validate()) {
            $query = User::find()->where(['or', 'chat_enable=1', 'role=1']);

            $query->andWhere(['=', 'status', 1]);
            $query->andWhere(['!=', 'id', $this->getCurrentUser()->id]);

            if ($form->partnerId) {
                $query->andWhere('provider_id=:provider_id', [':provider_id' => $form->partnerId]);
            }

            $users = $query->all();

            if (!$users) {
                return $this->sendJson([
                    'success' => false,
                    'message' => Yii::t('modules/chat', 'No chat users'),
                ]);
            }

            foreach ($users as $user) {
                $message = $this->module->chatMessageManager->createMessage(
                    $this->getCurrentUser()->id,
                    $user->id,
                    $form->message
                );
            }

            return $this->sendJson([
                'success' => true,
                'message' => Yii::t('modules/chat', 'Message has been sent'),
            ]);
        }

        return $this->sendJson([
            'success' => false,
            'message' => Yii::t('modules/chat', $form->getErrorSummary(true)[0]),
            'errors' => $form->errors,
        ]);



    }
}