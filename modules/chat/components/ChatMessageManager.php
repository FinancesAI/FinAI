<?php

namespace app\modules\chat\components;


use app\models\User;
use app\modules\chat\events\MessageEvent;
use app\modules\chat\jobs\CheckNewMessages;
use app\modules\chat\models\ChatConversation;
use app\modules\chat\models\ChatMessage;
use app\modules\chat\models\query\ChatConversationQuery;
use app\modules\chat\models\query\ChatMessageQuery;
use app\traits\CacheTrait;
use app\traits\CurrentUserTrait;
use Yii;
use yii\base\Component;
use yii\base\Event;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\Application;

/**
 *
 */
class ChatMessageManager extends Component
{
    use CurrentUserTrait, CacheTrait;

    const EVENT_BEFORE_MESSAGE_CREATE = 'onBeforeMessageCreate';
    const EVENT_AFTER_MESSAGE_CREATE = 'onAfterMessageCreate';
    const CACHE_KEY_NEW_CONVERSATIONS = 'newConversations';
    const CACHE_KEY_NEW_MESSAGES_COUNTERS = 'messagesNewCounters';

    /**
     * @var int
     */
    public int $delayBeforeNotification = 30;

    /**
     * @return void
     */
    public function init()
    {
        parent::init();

        $resetCounters = function (\yii\base\Event $event) {
            /** @var ChatMessage $message */
            $message = $event->sender;
            $this->resetCountersCache($message->to_user_id);
        };

        if (Yii::$app instanceof Application && !Yii::$app->user->isGuest) {
            Event::on(ChatMessage::class, ChatMessage::EVENT_AFTER_INSERT, $resetCounters);
            Event::on(ChatMessage::class, ChatMessage::EVENT_AFTER_UPDATE, $resetCounters);
            Event::on(ChatMessage::class, ChatMessage::EVENT_AFTER_DELETE, $resetCounters);
        }
    }

    /**
     * @param $user
     * @param string|null $searchQuery
     * @return array
     */
    public function getConversations($user, string $searchQuery = null): array
    {
        $providers = !$user->isAdmin() ? [$user->provider_id] : [];
        $statuses = [User::STATUS_ACTIVE];

        $query = $this->getConversationsQuery($user->id)
            ->with('lastMessage')
            ->withUserInfo(
                $statuses,
                $providers,
            )
            ->withContact()
            ->indexBy('contact_id')
            ->orderBy(['last_message_id' => SORT_DESC])
            ->limit(50);

        if ($query !== null) {
            $query
                ->andFilterWhere(['or',
                    ['like', 'sender.fullname', $searchQuery],
                    ['like', 'receiver.fullname', $searchQuery],
                ]);
        }

        return $query->all();
    }

    /**
     * @param $fromUserId
     * @param $toUserId
     * @return array
     */
    public function getMessages($fromUserId, $toUserId): array
    {
        $query = ChatMessage::find()
            ->between($fromUserId, $toUserId)
            ->withUserData($toUserId)
            ->withType($toUserId)
            ->limit(100)
            ->orderBy('id desc')
            ->indexBy('id');

        return (array) $query->all();
    }

    /**
     * @param $targetUserId
     * @param $ids
     * @return ChatMessage[]|array
     */
    public function getMessagesForUser($targetUserId, $ids = [])
    {
        $query = ChatMessage::find()->whereTargetUser($targetUserId);

        if ($ids !== false && count($ids)) {
            $query->andWhere(['in', 'id', $ids]);
        }

        return $query->all();
    }

    /**
     * @param $userId
     * @param $contactId
     * @param $limit
     * @param bool $history
     * @param null $key
     * @return ActiveDataProvider
     */
    public function getMessagesProvider($userId, $contactId, $limit, $history = true, $key = null)
    {
        $query = $this->getMessagesQuery($userId, $contactId);

        if (null !== $key) {
            $query->andWhere([$history ? '<' : '>', 'id', $key]);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $limit
            ]
        ]);
    }

    /**
     * @param $userId
     * @param bool $history
     * @param null $key
     * @return ActiveDataProvider
     */
    public function getConversationsProvider($userId, $history = true, $key = null)
    {
        $query = ChatConversation::find()->forUser($userId);
        if (null !== $key) {
            $query->andHaving([$history ? '<' : '>', 'last_message_id', $key]);
        }

        $query->indexBy('last_message_id');

        return new ActiveDataProvider([
            'query' => $query,
            'key' => 'last_message_id',
        ]);
    }

    /**
     * @param $userId
     * @return array|ActiveRecord[]
     */
    public function getNewMessagesCounters($userId)
    {
        $cacheKey = self::CACHE_KEY_NEW_MESSAGES_COUNTERS . '_' . $userId;
        $counters = $this->cache->get($cacheKey);

        if ($counters === false) {
            $counters = ChatMessage::find()
                ->onlyNew()
                ->select([
                    'from_user_id as contact_id',
                    'sum(is_new) AS new_messages_count',
                ])
                ->andWhere(['to_user_id' => $userId, 'is_deleted_by_receiver' => 0])
                ->groupBy('from_user_id')
                ->indexBy('contact_id')
                ->asArray()
                ->all();

            $this->cache->set($cacheKey, $counters, 3600);
        }

        return $counters;
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function getNewMessagesCount($userId)
    {
        $cacheKey = self::CACHE_KEY_NEW_CONVERSATIONS . '_' . $userId;
        $count = $this->cache->get($cacheKey);

        if ($count === false) {
            $count = (int) ChatMessage::find()
                ->onlyNew()
                ->andWhere(['to_user_id' => $userId, 'is_deleted_by_receiver' => 0])
                ->sum('is_new');

            $this->cache->set($cacheKey, $count, 3600);
        }

        return $count;
    }

    /**
     * @param $fromId
     * @param $contactId
     * @param $text
     * @return ChatMessage
     */
    public function createMessage($fromId, $contactId, $text): ChatMessage
    {
        $message = new ChatMessage(['scenario' => ChatMessage::SCENARIO_CREATE]);
        $message->from_user_id = $fromId;
        $message->to_user_id = $contactId;
        $message->text = $text;

        $event = new MessageEvent;
        $event->message = $message;
        $this->trigger(self::EVENT_BEFORE_MESSAGE_CREATE, $event);

        if ($event->isValid) {
            $message->save();
            $this->trigger(self::EVENT_AFTER_MESSAGE_CREATE, $event);
            $job = new CheckNewMessages();
            $job->userId = $contactId;
            Yii::$app->queue->delay($this->delayBeforeNotification)->push($job);
        }

        $this->resetCountersCache($contactId);

        return $message;
    }

    /**
     * @param $userId
     * @param $ids
     * @return int
     */
    public function deleteMessages($userId, $ids)
    {
        $messages = $this->getMessagesForUser($userId, $ids);
        $count = 0;

        foreach ($messages as $message) {
            if ($message->from_user_id == Yii::$app->user->id) {
                $message->is_deleted_by_sender = 1;
            } else {
                $message->is_deleted_by_receiver = 1;
            }
            if ($message->save()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param $fromUserId
     * @param $toUserId
     * @return ChatMessageQuery
     */
    protected function getMessagesQuery($fromUserId, $toUserId)
    {
        return ChatMessage::find()
            ->orderBy(['id' => SORT_DESC])
            ->between($fromUserId, $toUserId);
    }

    /**
     * @param $userId
     * @return ChatConversationQuery
     */
    protected function getConversationsQuery($userId)
    {
        return ChatConversation::find()
            ->forUser($userId);
    }

    /**
     * @param $userId
     * @param $contactId
     * @return array the number of rows updated
     */
    public function deleteConversation($userId, $contactId)
    {
        $count = ChatConversation::updateAll([
            'is_deleted_by_sender' => new Expression('IF([[from_user_id]] = :userId, TRUE, is_deleted_by_sender)'),
            'is_deleted_by_receiver' => new Expression('IF([[to_user_id]] = :userId, TRUE, is_deleted_by_receiver)')
        ], ['or',
            ['to_user_id' => new Expression(':userId'), 'from_user_id' => $contactId, 'is_deleted_by_receiver' => false],
            ['from_user_id' => new Expression(':userId'), 'to_user_id' => $contactId, 'is_deleted_by_sender' => false],
        ], [
            'userId' => $userId
        ]);

        return compact('count');
    }


    /**
     * @param $userId
     * @param $contactId
     * @return array the number of rows updated
     */
    public function readConversation($userId, $contactId)
    {
        $mutexName = 'readConversation_' . $userId . '_' . $contactId;
        $count = 0;

        if (Yii::$app->mutex->acquire($mutexName)) {
            $count = ChatConversation::updateAll(['is_new' => false], [
                'to_user_id' => $userId,
                'from_user_id' => $contactId,
                'is_new' => true
            ]);

            $this->resetCountersCache($userId);

            Yii::$app->mutex->release($mutexName);
        }

        return ['count' => $count];
    }

    /**
     * @param $userId
     * @param $contactId
     * @return array
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function unreadConversation($userId, $contactId)
    {
        /** @var ChatMessage $message */
        $message = ChatMessage::find()
            ->where(['from_user_id' => $contactId, 'to_user_id' => $userId, 'is_deleted_by_receiver' => false])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();

        $count = 0;
        if ($message) {
            $message->is_new = 1;
            $count = intval($message->update());
        }

        return compact('count');
    }

    /**
     * @param $userId
     */
    protected function resetCountersCache($userId)
    {
        $this->cache->delete(self::CACHE_KEY_NEW_CONVERSATIONS . '_' . $userId);
        $this->cache->delete(self::CACHE_KEY_NEW_MESSAGES_COUNTERS . '_' . $userId);
    }

    /**
     * @param $fromUserId
     * @param $toUserId
     * @return ChatMessage[]|array
     */
    public function getMessagesToUser($fromUserId, $toUserId)
    {
        return ChatMessage::find()->whereSender($fromUserId)->whereReceiver($toUserId)->all();
    }
}
