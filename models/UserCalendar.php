<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_calendar}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $title
 * @property string $start
 * @property string $end
 *
 * @property User $user
 */
class UserCalendar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_calendar}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'title'], 'required'],
            [['user_id','start','end'], 'integer'],
            [['title'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/user', 'ID'),
            'user_id' => Yii::t('app/user', 'User ID'),
            'embed' => Yii::t('app/user', 'Embed'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
