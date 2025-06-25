<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "page".
 *
 * @property string $id
 * @property string $title
 * @property string $content
 * @property string $custom_id
 * @property string $in_menu
 * @property string $menu_title
 * @property string $api_type
 */
class Page extends \yii\db\ActiveRecord
{
	/**
	 * Link hash
	 * @var string
	 */
	private $link;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'page';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
        		[['content'], 'string'],
        		[['in_menu'], 'safe'],
        		[['title'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/page', 'ID'),
            'title' => Yii::t('app/page', 'Title'),
            'content' => Yii::t('app/page', 'Content'),
            'custom_id' => Yii::t('app/page', 'Custom ID'),
            'in_menu' => Yii::t('app/page', 'Show in menu'),
            'menu_title' => Yii::t('app/page', 'Menu title'),
            'api_type' => Yii::t('app/page', 'Page api'),
            'order_by' => Yii::t('app/page', 'Order'),
        		
        ];
    }
    
}
