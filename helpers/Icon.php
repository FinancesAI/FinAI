<?php

namespace app\helpers;

use yii\helpers\Html;

/**
 *
 */
class Icon
{
    /**
     * @param $iconName
     * @param array $options
     * @return string
     */
    public static function fe($iconName, array $options = []): string
    {
        return self::icon('fe', $iconName, $options);
    }

    /**
     * @param $iconName
     * @param array $options
     * @return string
     */
    public static function fa($iconName, array $options = []): string
    {
        return self::icon('fa', $iconName, $options);
    }

    /**
     * @param $iconClass
     * @param $iconName
     * @param array $options
     * @return string
     */
    public static function icon($iconClass, $iconName, array $options = []): string
    {
        if (isset($options['class'])) {
            $options['class'] = "$iconClass $iconClass-$iconName {$options['class']}";
        } else {
            $options['class'] = "$iconClass $iconClass-$iconName";
        }

        return Html::tag('i', '', $options);
    }
}
