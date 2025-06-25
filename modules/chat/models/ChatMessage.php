<?php

namespace app\modules\chat\models;

use app\base\ActiveRecord;
use app\models\User;
use app\modules\chat\models\query\ChatMessageQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * @package app\models
 *
 * @property string $id
 * @property int $from_user_id
 * @property int $to_user_id
 * @property string $text
 * @property int $is_new
 * @property int $is_deleted_by_sender
 * @property int $is_deleted_by_receiver
 * @property int $created_at
 *
 * @property User $sender
 * @property User $receiver
 */
class ChatMessage extends ActiveRecord
{
    const TYPE_INBOX = 'inbox';
    const TYPE_SENT = 'sent';

    /**
     * @inheritdoc
     * @return ChatMessageQuery the active query used by this AR class.
     */
    public static function find(): ChatMessageQuery
    {
        return new ChatMessageQuery(get_called_class());
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%chat_message}}';
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['from_user_id', 'to_user_id'], 'required'],
            [
                [
                    'from_user_id',
                    'to_user_id',
                    'is_new',
                    'is_deleted_by_sender',
                    'is_deleted_by_receiver',
                    'created_at'
                ],
                'integer'
            ],
            [['text'], 'string', 'max' => 1000],
            [['created_at'], 'integer'],
            [
                ['from_user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['from_user_id' => 'id']
            ],
            [
                ['to_user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['to_user_id' => 'id']
            ],
            ['from_user_id', 'compare', 'compareAttribute' => 'to_user_id', 'operator' => '!='],
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['to_user_id', 'text'];

        return $scenarios;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'type',
            'contact_id',
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'from_user_id' => Yii::t('modules/chat', 'Sender'),
            'to_user_id' => Yii::t('modules/chat', 'Receiver'),
            'text' => Yii::t('modules/chat', 'Message'),
            'is_new' => Yii::t('modules/chat', 'New'),
            'is_deleted_by_sender' => Yii::t('modules/chat', 'Deleted'),
            'is_deleted_by_receiver' => Yii::t('modules/chat', 'Deleted'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getSender(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'from_user_id'])->alias('sender');
    }

    /**
     * @return ActiveQuery
     */
    public function getReceiver(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'to_user_id'])->alias('receiver');
    }

    /**
     * @return false|string
     */
    public function getCreatedDateTime()
    {
        return gmdate('Y-m-d H:i:s', $this->created_at);
    }

    /**
     * @param $userId
     * @return bool
     */
    public function hasAccess($userId): bool
    {
        return $this->from_user_id == $userId || $this->to_user_id == $userId;
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'id',
            'contact_id',
            'from_user_id',
            'to_user_id',
            'datetime' => function ($model) {
                $dt = new \DateTime('@' . $model->created_at);
                $dt->setTimeZone(new \DateTimeZone(Yii::$app->timeZone));

                return $dt->format('Y-m-d H:i:s');
            },
            'type',
            'text',
            'is_new',
            'user' => function (ChatMessage $message) {
                $user = $message->sender;
                return [
                    'id' => $user->id,
                    'avatar' => $user->getAvatarUrl(48, 48),
                    'full_name' => $user->fullname,
                    'online' => $user->isOnline,
                    'admin' => $user->isAdmin(),
                ];
            }
        ];
    }
}
