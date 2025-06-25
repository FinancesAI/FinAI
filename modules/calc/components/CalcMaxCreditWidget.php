<?php

declare(strict_types=1);

namespace app\modules\calc\components;

use yii\base\Widget;

/**
 *
 */
class CalcMaxCreditWidget extends Widget
{
    /**
     * @return string
     */
    public function run(): string
    {
        return $this->render('_calc_max_credit');
    }

    /**
     * @return string
     */
    public function getViewPath(): string
    {
        return '@modules/calc/views/';
    }
}