<?php


namespace app\base\grid;

use Yii;

class ActionColumn extends \yii\grid\ActionColumn
{
    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        $this->initDefaultButton('view', 'eye-open', [
            'class' => 'text-primary'
        ]);
        $this->initDefaultButton('update', 'pencil', [
            'class' => 'text-success'
        ]);
        $this->initDefaultButton('delete', 'trash', [
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method' => 'post',
            'class' => 'text-danger'
        ]);
    }
}
