<?php

namespace app\modules\chat\forms;

use Yii;
use yii\base\Model;

/**
 * @package app\forms
 */
class NewsLetterForm extends Model
{
    /**
     * @var int|null
     */
    public ?int $partnerId = null;

    /**
     * @var string|null
     */
    public ?string $message = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['message'], 'required'],
            ['message', 'string', 'min' => 1, 'max' => 1000],
            ['partnerId', 'safe'],
        ];
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }
}
