<?php

namespace app\modules\chat\models;

use app\models\User;
use app\modules\chat\models\query\ChatAdminQuery;

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
class ChatAdmin extends User
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
     * @return ChatAdminQuery
     */
    public static function find(): ChatAdminQuery
    {
        return new ChatAdminQuery(get_called_class());
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
    public function fields(): array
    {
        return [
            'id'=> function () {
                return null;
            },
            'uid' => function (ChatAdmin $model) {
                return md5($model->id . $model->fullname);
            },
            'last_message_id'=> function () {
                return null;
            },
            'last_message' => function () {
                return null;
            },
            'contact' => function (ChatAdmin $model) {
                return [
                    'id' => $model->id,
                    'avatar' => $model->getAvatarUrl(48, 48),
                    'full_name' => $model->fullname,
                    'online' => $model->isOnline,
                    'admin' => (bool)$model->isAdmin(),
                ];
            },
            'created_at' => function () {
                return null;
            },
        ];
    }
}
