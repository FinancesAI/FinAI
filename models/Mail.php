<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mail".
 *
 * @property string $id
 * @property string $title
 * @property string $content
 * @property string $custom_id
 * @property string $in_menu
 * @property string $menu_title
 * @property string $api_type
 * @property int $order_by [int(10) unsigned]
 */
class Mail extends \yii\db\ActiveRecord
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
        return 'mail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['content'], 'string'],
            [['title', 'menu_title'], 'string', 'max' => 250],
            [['custom_id'], 'integer', 'max' => 30],
        	[['in_menu', 'api_type', 'order_by'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/mail', 'ID'),
            'title' => Yii::t('app/mail', 'Title'),
            'content' => Yii::t('app/mail', 'Content'),
            'custom_id' => Yii::t('app/mail', 'Custom ID'),
            'in_menu' => Yii::t('app/mail', 'Show in menu'),
            'menu_title' => Yii::t('app/mail', 'Menu title'),
            'api_type' => Yii::t('app/mail', 'Mail api'),
            'order_by' => Yii::t('app/mail', 'Order'),
        		
        ];
    }
    
    public function setLink($link) {
    	$this->link = $link;
    }
    
    public function getLink($source) {
    	
    	if ($this->link) {
    		if ($source == "latfinance") {
    			return \Yii::$app->params["maillink"].$this->link;	
    		}
    		if ($source == "source2") {
    			return \Yii::$app->params["maillink2"].$this->link;	
    		}
    	}
    	
    	return null;
    }
    
    public function getSubject($source) {
    	if ($source == "latfinance") {
    		$source = "latfinance";
    	}
    	if ($source == "latfinance") {
    		$source = "latfinance";
    	}
    	
    	return str_replace("[source]", $source, $this->title);
    }
    
    public function getHtml($source, $model) {
    	$layout = Mail::find()->where(["title" => "layout_".$source])->one();
    	 
    	$msg = $this->content; //str_replace("", $this->content, $layout->content);
    	
    	return $msg;
    }
    
    private function _parseText($text, $model) {
    	if (!$model) {
    		return $text;
    	}
    	
    	return str_replace([
    			"[name]",
    			"[surname]",
    			"[source]",
    			"[id]",
    			"[vsaa_link]",
    			"[vsaa_ep52_link]",
    			"[bill_extra_date]",
    			"[bill_id]",
    			"[bill_amount]",
    			"[post_custom]",
    			"[post_amount]",
    			"[post_max_amount]",
    			"[link]",
    			"[period12_mm_yyyy]",
    			"[period12_dd_mm_yyyy]",
    			"[period6_mm_yyyy]",
    			"[period6_dd_mm_yyyy]",
    	],
    	[
    			$model->person->name,
    			$model->person->surname,
    			$model->source,
    			$model->id,
    			$this->_getVsaaLink($model->source),
    			$this->_getVsaaEp52Link($model->source),
    			$this->_getBillExtraDate(),
    			$model->id,
    			($model->bill ? $model->bill->amount : null),
    			(IS_WEB ? \Yii::$app->getRequest()->post("email_custom_text") : ""),
    			(IS_WEB ? \Yii::$app->getRequest()->post("amount") : ""),
    			(IS_WEB ? \Yii::$app->getRequest()->post("max_amount") : ""),
    			$this->getLink($model->source),
    			date("m.Y", strtotime("-12 months"))." - ".date("m.Y"),
    			date("d.m.Y", strtotime("-12 months"))." - ".date("d.m.Y"),
    			date("m.Y", strtotime("-6 months"))." - ".date("m.Y"),
    			date("d.m.Y", strtotime("-6 months"))." - ".date("d.m.Y"),
    	], $text);
    }
    
    private function _getVsaaLink($source) {
    	return (($source == "latfinance") ? "https://".$source.".lv/vsaa/" : "https://".$source.".lv/vsaa-izzina/");
    }
    
    private function _getVsaaEp52Link($source) {
    	return (($source == "latfinance") ? "https://".$source.".lv/vsaa-ep52/" : "https://".$source.".lv/vsaa-izzina-ep52/");
    }
    
    private function _getBillExtraDate() {
    	return date("d.m.Y", strtotime('+5 day', time()));
    }
}
