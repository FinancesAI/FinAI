<?php

namespace app\modules\chat\events;

use app\modules\chat\models\ChatMessage;
use app\base\Event;

/**
 *
 */
class MessageEvent extends Event
{
    /**
     * @var ChatMessage
     */
    public ChatMessage $message;
}
