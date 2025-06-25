<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;


/**
 * This is the model class for table "{{%log}}".
 *
 * @property string $id
 * @property integer $type
 * @property string $data
 * @property integer $create_time
 * @property integer $update_time
 */
class Log extends \yii\db\ActiveRecord
{
	const TYPE_LOGIN_FAIL = 1;
	const TYPE_LOGIN_SUCCESS = 2;
	const TYPE_EXPORT = 3;
	const TYPE_USER_CREATE = 4;
	const TYPE_USER_UPDATE = 5;
	const TYPE_SYSTEM_UPDATE = 6;
	const TYPE_LOGIN_FAIL_IP = 7;
	
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'data'], 'required'],
            [['type', 'create_time', 'update_time'], 'integer'],
            [['data'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
    	return [
    			[
    					'class' => TimestampBehavior::className(),
    					'createdAtAttribute' => 'create_time',
    					'updatedAtAttribute' => 'update_time',
    					'value' => new Expression(time()),
    			],
    	];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
    	return [
    			'id' => Yii::t('app/log', 'ID'),
    			'type' => Yii::t('app/log', 'Type'),
    			'data' => Yii::t('app/log', 'Data'),
    			'create_time' => Yii::t('app/log', 'Create Time'),
    			'update_time' => Yii::t('app/log', 'Update Time'),
    	];
    }
    
    public function create($type)
    {
    	if (isset($_POST["LoginForm"]["password"]))
    		unset($_POST["LoginForm"]["password"]);
    	
    	$log = new Log();
    	$log->type = $type;
    	$log->data = serialize($_POST);
    	$log->save();
    }
    
    /**
     * @inheritdoc
     */
    public function typeLabels()
    {
        return [
            '1' => Yii::t('app/log', 'Failed login'),
            '2' => Yii::t('app/log', 'Success login'),
            '3' => Yii::t('app/log', 'Export data'),
            '4' => Yii::t('app/log', 'User created'),
            '5' => Yii::t('app/log', 'User updated'),
            '6' => Yii::t('app/log', 'System update'),
            '7' => Yii::t('app/log', 'Failed login due IP validation'),
        ];
    }
}
