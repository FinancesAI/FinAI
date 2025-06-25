<?php

namespace app\base;

/**
 * @package app\base
 */
class Event extends \yii\base\Event
{
    /**
     * @var mixed
     */
    public $extraData;
    /**
     * @var bool
     */
    public $isValid = true;
    /**
     * @var boolean
     */
    public $override = false;
}
