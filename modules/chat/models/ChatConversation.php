<?php

namespace app\modules\chat\models;

use app\base\ActiveRecord;
use app\models\User;
use app\modules\chat\models\query\ChatConversationQuery;
use yii\db\ActiveQuery;
use yii\helpers\StringHelper;

/**
 * @package app\models
 *
 * @property int $user_id
 * @property int $contact_id
 * @property int $last_message_id
 *
 * @property User $sender
 * @property User $receiver
 */
class ChatConversation extends ActiveRecord
{
    /**
     * @var array
     */
    public array $newMessagesCounts;
    /**
     * @var string
     */
    public string $uid;

    /**
     * @return ActiveQuery
     */
    public function getLastMessage(): ActiveQuery
    {
        return $this->hasOne(ChatMessage::class, ['id' => 'last_message_id'])->orderBy('created_at desc');
    }

    /**
     * @return ActiveQuery
     */
    public function getNewMessages(): ActiveQuery
    {
        return $this->hasMany(ChatMessage::class, ['from_user_id' => 'contact_id', 'to_user_id' => 'user_id'])
            ->andOnCondition(['is_new' => true]);
    }

    /**
     * @return ChatConversationQuery
     */
    public static function find(): ChatConversationQuery
    {
        return new ChatConversationQuery(get_called_class());
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return ChatMessage::tableName();
    }

    /**
     * @return ActiveQuery
     */
    public function getSender(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'from_user_id'])->alias('sender');
    }


    /**
     * @return ActiveQuery
     */
    public function getReceiver(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'to_user_id'])->alias('receiver');
    }


    /**
     * @param $insert
     * @return bool
     */
    public function beforeSave($insert): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function beforeDelete(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function beforeValidate(): bool
    {
        return false;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'user_id',
            'user_online',
            'created_at',
            'contact_id',
            'last_message_id',
            'new_messages_count',
        ]);
    }

    /**
     * @param $record
     * @param $row
     * @return void
     */
    public static function populateRecord($record, $row)
    {
        foreach (['id', 'user_id', 'contact_id'] as $name) {
            if (isset($row[$name])) {
                $row[$name] = intval($row[$name]);
            }
        }
        parent::populateRecord($record, $row);
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'id',
            'uid' => function (ChatConversation $conversation) {
                return md5($conversation->id . $conversation->last_message_id);
            },
            'last_message_id',
            'last_message' => function ($model) {
                return [
                    'text' => StringHelper::truncate($model['lastMessage']['text'], 20),
                    'date' => $model['lastMessage']['created_at'],
                    'fromUserId' => $model['lastMessage']['from_user_id']
                ];
            },
            'contact' => function (ChatConversation $model) {
                if ($model->contact_id == $model->from_user_id) {
                    $user = $model->sender;
                } else {
                    $user = $model->receiver;
                }
                return [
                    'id' => $user->id,
                    'avatar' => $user->getAvatarUrl(48, 48),
                    'full_name' => $user->fullname,
                    'online' => $user->isOnline,
                    'admin' => (bool)$user->isAdmin(),
                ];
            },
            'created_at',
        ];
    }
}
