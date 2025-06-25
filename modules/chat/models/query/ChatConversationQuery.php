<?php

namespace app\modules\chat\models\query;

use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 *
 */
class ChatConversationQuery extends ActiveQuery
{
    /**
     * @return void
     */
    public function init()
    {
        parent::init();
        $this
            ->alias('c')
            ->select([
                'c.*',
                'last_message_id' => new Expression('MAX([[c.id]])'),
            ])
            ->andWhere([
                'or',
                ['to_user_id' => new Expression(':userId'), 'is_deleted_by_receiver' => false],
                ['from_user_id' => new Expression(':userId'), 'is_deleted_by_sender' => false],
            ])
            ->groupBy('contact_id');
    }

    /**
     * @param array $statuses
     * @param array $providers
     * @param array $role
     * @return ChatConversationQuery
     */
    public function withUserInfo(array $statuses = [], array $providers = [], array $role = []): ChatConversationQuery
    {
        $query = $this->joinWith(['sender', 'receiver']);

        if ($statuses) {
            $query->andWhere([
                'or',
                ['in', 'sender.status', $statuses],
                ['in', 'receiver.status', $statuses],
            ]);
        }

        if ($providers) {
            $query->andWhere([
                'or',
                ['in', 'sender.provider_id', $providers],
                ['in', 'receiver.provider_id', $providers],
            ]);
        }

        if ($role) {
            $query->andWhere([
                'or',
                ['in', 'sender.role', $role],
                ['in', 'receiver.role', $role],
            ]);
        }

        return $query;
    }

    /**
     * @return ChatConversationQuery
     */
    public function withContact(): ChatConversationQuery
    {
        return $this->addSelect([
            'contact_id' => new Expression('IF([[from_user_id]] = :userId, [[to_user_id]], [[from_user_id]])')
        ]);
    }

    /**
     * @param int $userId
     * @return ChatConversationQuery
     */
    public function forUser(int $userId): ChatConversationQuery
    {
        return $this->addParams(['userId' => $userId]);
    }
}
