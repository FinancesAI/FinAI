<?php

namespace app\modules\chat\components;


use app\models\User;
use app\modules\chat\models\ChatAdmin;
use app\modules\chat\models\ChatContact;
use app\modules\chat\models\query\ChatAdminQuery;
use app\modules\chat\models\query\ChatContactQuery;
use yii\base\Component;

/**
 *
 */
class UserManager extends Component
{
    /**
     * @param $user
     * @param $searchQuery
     * @return array
     */
    public function getContacts($user, $searchQuery = null): array
    {
        $query = $this->getContactsQuery($user->id)
            ->indexBy('id')
            ->orderBy(['last_login_time' => SORT_DESC])
            ->limit(50);

        if ($query !== null) {
            $query->andFilterWhere(['like', 'fullname', $searchQuery]);
        }

        if (!$user->isAdmin()) {
            $query->andWhere(['in', 'provider_id', [$user->provider_id]]);
        }

        $query->andWhere(['in', 'status', [User::STATUS_ACTIVE]]);

        $contacts = $query->all();

        if (!$user->isAdmin()) {
            $chatAdmins = $this->getChatAdmins($user, $searchQuery);
            $contacts = $contacts + $chatAdmins;
        }

        return $contacts;
    }

    /**
     * @param $user
     * @param $searchQuery
     * @return array
     */
    public function getChatAdmins($user, $searchQuery = null): array
    {
        $query = $this->getChatAdminsQuery($user->id)
            ->indexBy('id')
            ->orderBy(['last_login_time' => SORT_DESC]);

        if ($query !== null) {
            $query->andFilterWhere(['like', 'fullname', $searchQuery]);
        }

        return $query->all();
    }

    /**
     * @param $userId
     * @return ChatContactQuery
     */
    protected function getContactsQuery($userId): ChatContactQuery
    {
        return ChatContact::find()->forUser($userId);
    }

    /**
     * @param $userId
     * @return ChatAdminQuery
     */
    protected function getChatAdminsQuery($userId): ChatAdminQuery
    {
        return ChatAdmin::find()->forUser($userId);
    }

}
