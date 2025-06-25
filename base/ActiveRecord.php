<?php

namespace app\base;

use app\traits\CacheTrait;

/**
 * @package app\base
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    use CacheTrait;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
}
