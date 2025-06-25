<?php

namespace app\modules\chat\forms;

use app\models\User;
use Yii;
use yii\base\Model;
use yii\web\Application as WebApplication;

/**
 * @package app\forms
 */
class MessageForm extends Model
{
    /**
     * @var int
     */
    public int $contactId;

    /**
     * @var string
     */
    public string $message;

    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            [['contactId', 'message'], 'required'],
            [
                'contactId',
                'exist',
                'targetClass' => User::class,
                'targetAttribute' => ['contactId' => 'id']
            ],
            ['message', 'string', 'min' => 1, 'max' => 1000],
        ];

        if (Yii::$app instanceof WebApplication) {
            $rules[] = ['contactId', 'compare', 'compareValue' => Yii::$app->user->id, 'operator' => '!='];
        }

        return $rules;
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }
}
