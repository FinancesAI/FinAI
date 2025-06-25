<?php

namespace app\services;

use app\models\Provider;
use yii\helpers\ArrayHelper;

/**
 *
 */
class ProviderService
{
    /**
     * @return array
     */
    public static function getProvidersList(): array
    {
        return Provider::find()->select(['name', 'id'])->indexBy('id')->column();
    }
}