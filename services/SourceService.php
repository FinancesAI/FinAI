<?php

namespace app\services;

use app\models\Source;
use yii\helpers\ArrayHelper;

/**
 *
 */
class SourceService
{
    /**
     * @return array
     */
    public static function getWordpressForms(): array
    {
        $sources = Source::find()->orderBy('id')->asArray()->all();
        return ArrayHelper::index($sources, 'id');
    }

    /**
     * @return array
     */
    public static function getFormToSource(): array
    {
//        $sources = Source::find()->all();
//        return ArrayHelper::map($sources, 'wordpress_id', 'id');

        return Source::find()->select(['id', 'wordpress_id'])->indexBy('wordpress_id')->column();
    }

}