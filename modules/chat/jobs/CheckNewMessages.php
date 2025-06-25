<?php

namespace app\modules\chat\jobs;

use app\modules\chat\models\ChatMessage;
use app\models\User;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 *
 */
class CheckNewMessages extends BaseObject implements JobInterface
{

    /**
     * @var int
     */
    public int $userId;
    /**
     * @var int
     */
    public int $secondsSinceLastCheck = 60;

    /**
     * @param $queue
     * @return void
     */
    public function execute($queue)
    {

        if (Yii::$app->setting->get("receive_email_on_new_messages") != 1) {
            return;
        }

        $user = User::findOne(['id' => $this->userId]);

        if ($user == null) {
            return;
        }

        if (isset($user->lang)) {
            Yii::$app->language = $user->lang;
        }

        if ($user->new_messages_email_time !== null && time(
            ) - $user->new_messages_email_time < $this->secondsSinceLastCheck) {
            return;
        }

        $query = ChatMessage::find()->whereReceiver($user->id)->onlyNew();
        if ($user->last_new_message_id !== null) {
            $query->andWhere(['>', 'id', $user->last_new_message_id]);
        }

        $messages = $query->all();

        if (count($messages) == 0) {
            return;
        }

        $lastMessageId = ChatMessage::find()->whereTargetUser($user->id)->onlyNew()->max('id');

        $user->new_messages_email_time = time();
        $user->last_new_message_id = $lastMessageId;


        $user->save();


        Yii::$app->getModule('chat')->chatMailer->sendMessage(
            $user->email,
            Yii::t('modules/chat', 'You have new messages'),
            'new-messages', [
                'user' => $user,
                'messages' => $messages,
            ]
        );

    }
}
