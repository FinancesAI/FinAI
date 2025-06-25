<?php

namespace app\modules\chat\models\query;

use app\models\User;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 *
 */
class ChatAdminQuery extends ActiveQuery
{
    public function init()
    {
        parent::init();

        $this->andWhere(['!=', 'id', new Expression(':userId')]);
        $this->andWhere(['=', 'role', User::ROLE_ADMIN]);
    }

    /**
     * @param int $userId
     * @return ChatAdminQuery
     */
    public function forUser(int $userId): ChatAdminQuery
    {
        return $this->addParams(['userId' => $userId]);
    }

}
