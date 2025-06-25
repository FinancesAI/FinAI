<?php

namespace app\traits;

use app\models\User;
use Yii;
use yii\web\IdentityInterface;

/**
 * Trait CurrentUserTrait
 * @property null|IdentityInterface|User $currentUser
 */
trait CurrentUserTrait
{
    /**
     * @return IdentityInterface|null
     */
    public function getCurrentUser(): ?IdentityInterface
    {
        if (!Yii::$app->user->isGuest) {
            return Yii::$app->user->identity;
        }

        return null;
    }

}
