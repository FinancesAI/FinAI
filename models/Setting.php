<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%setting}}".
 *
 * @property string $id
 * @property string $name
 * @property string $value
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%setting}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['name', 'value'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/setting', 'ID'),
            'name' => Yii::t('app/setting', 'Name'),
            'value' => Yii::t('app/setting', 'Value'),
        ];
    }
    
    public function get($name) {
    	if (!$settings = Yii::$app->cache->get("setting_cache")) {
    		$settings = Setting::find()->all();
    		Yii::$app->cache->set("setting_cache", $settings);
    	}
    	foreach ($settings as $setting) {
    		if ($setting->name == $name)
    			return $setting->value;
    	}
    	
    	return null;
    }
    
    public function afterSave($insert, $changedAttributes) {
    	parent::afterSave($insert, $changedAttributes);
    	
    	Yii::$app->cache->flush();
    }
}
