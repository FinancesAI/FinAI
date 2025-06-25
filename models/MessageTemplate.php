<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "message_template".
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $content
 *
 * @property User $user
 */
class MessageTemplate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'message_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'title'], 'required'],
            [['user_id'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/message-template', 'ID'),
            'user_id' => Yii::t('app/message-template', 'User ID'),
            'title' => Yii::t('app/message-template', 'Title'),
            'content' => Yii::t('app/message-template', 'Content'),
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
