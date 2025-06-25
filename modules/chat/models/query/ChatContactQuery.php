<?php

namespace app\modules\chat\models\query;

use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * @package app\models\query
 */
class ChatContactQuery extends ActiveQuery
{
    public function init()
    {
        parent::init();

        $this->andWhere(['!=', 'id', new Expression(':userId')]);
    }

    /**
     * @param int $userId
     * @return ChatContactQuery
     */
    public function forUser(int $userId): ChatContactQuery
    {
        return $this->addParams(['userId' => $userId]);
    }

}
