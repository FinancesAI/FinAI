<?php

namespace app\models;

use Yii;

use yii\log\Logger;

/**
 * This is the model class for table "{{%changes}}".
 *
 * @property string $id
 * @property integer $type
 * @property string $type_id
 * @property string $attr
 * @property string $attr_from
 * @property string $attr_to
 * @property integer $create_time
 * @property int $user_id [int(10) unsigned]
 */
class Changes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%changes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'type_id', 'attr', 'create_time'], 'required'],
            [['type', 'type_id', 'create_time'], 'integer'],
            [['attr_from', 'attr_to'], 'safe'],
            [['attr'], 'string', 'max' => 250],
        ];
    }

    public static function setChanges($type, $type_id, $attr, $attr_from, $attr_to) {
    	if ($attr == "waiting_time")
    		return ;
    	
    	$user = null;
    	if (method_exists(Yii::$app, "getUser") && method_exists(Yii::$app->getUser(), "getIdentity")) {
    		if (\Yii::$app->getUser() && \Yii::$app->getUser()->getIdentity())
				$user = \Yii::$app->getUser()->getIdentity()->getId();
    	}
    	
    	if ($attr == "description" && $attr_to == "") {
    		return;
    	}
    	
    	$new = new Changes();
    	$new->type = $type;
    	$new->type_id = $type_id;
    	$new->create_time = time();
    	$new->user_id = $user;
    	$new->attr = $attr;
    	$new->attr_from = $attr_from;
    	$new->attr_to = $attr_to;
    	$new->save();
    }

    public static function getChanges($type, $type_id, $onlyDesc = false) {
    	
    	if ($onlyDesc) {
    		$models = Changes::find()
    		->where(["in", "type", $type])
    		->andWhere(["in", "type_id", $type_id])
    		->andWhere(["attr" => "description"])
    		->orderBy("id desc")
    		->offset(\Yii::$app->getRequest()->post("offset"))
    		->limit(\Yii::$app->params["changes_limit"])
    		->all();
    		 
    		$more = Changes::find()
    		->where(["in", "type", $type])
    		->andWhere(["in", "type_id", $type_id])
    		->andWhere(["attr" => "description"])
    		->orderBy("id desc")
    		->offset(\Yii::$app->getRequest()->post("offset")+count($models))
    		->limit(1)
    		->one();
    	} else {
    		$models = Changes::find()
    		->where(["in", "type", $type])
    		->andWhere(["in", "type_id", $type_id])
    		->andWhere(["!=", "attr", "description"])
    		->andWhere(["!=", "attr", "reminder_class"])
    		->orderBy("id desc")
    		->offset(\Yii::$app->getRequest()->post("offset"))
    		->limit(\Yii::$app->params["changes_limit"])
    		->all();
    		 
    		$more = Changes::find()
    		->where(["in", "type", $type])
    		->andWhere(["in", "type_id", $type_id])
    		->andWhere(["!=", "attr", "description"])
    		->andWhere(["!=", "attr", "reminder_class"])
    		->orderBy("id desc")
    		->offset(\Yii::$app->getRequest()->post("offset")+count($models))
    		->limit(1)
    		->one();
    	}
    	
    	$more = ($more ? 1 : 0);
    	
    	$html = "";
    	if ($models) {
    		foreach ($models as $model) {
    			$user = ($model->user ? $model->user->fullname : "System");
    		
    			$html .= "<div class=\"list-group-item\" data-toggle=\"tooltip\" data-placement=\"left\" title=\"Loan ID - ".$model->type_id."\">";
    			$html .= "<p class=\"list-group-item-text changes-text-top\">
		    		<b>".self::getAttrLabel($model->attr, $model->type)."</b> ".\Yii::$app->getFormatter()->asDate($model->create_time)." (".$user.")</p><p class=\"list-group-item-text changes-text-bottom\">
		    		".self::getAttrText($model)."</p>";
    			$html .= "</div>";
    		}
    	}
    	
    	return json_encode(["html" => $html, "more" => $more]);
    }
    
    public static function getAttrLabel($attr, $type) {
    	if  (substr($attr, 0, 8) === "actions_") {
    		if (array_key_exists(str_replace("actions_", "", $attr), (new Loan())->getActions()))
    			return (new Loan())->getActions()[str_replace("actions_", "", $attr)];
    		return "Unknow action";
    	}
    	
        if ($type == Loan::TYPE) {
    		return (new Loan())->getAttributeLabel($attr);
    	}
    	
    	if ($type == Person::TYPE) {
    		return (new Person())->getAttributeLabel($attr);
    	}
    	
    	if ($type == LoanExtra::TYPE) {
    		return (new Person())->getAttributeLabel($attr);
    	}
    	
    	if ($type == LoanProgerss::TYPE) {
    		return (new LoanProgerss())->getAttributeLabelFormated($attr);
    	}
    	
    	return "Unknown";
    }
    
    public static function getAttrValue($model, $value) {
    	if  (substr($model->attr, 0, 8) === "actions_") {
    		return $value;
    	}

    	if ($model->type == Loan::TYPE) {
    		$loan = new Loan();
    		$loan->{$model->attr} = $value;
    		return $loan->getValue($model->attr, $loan);
    	}

    	if ($model->type == LoanProgerss::TYPE) {
    		$parts = explode("-", $model->attr);
    		$loan = new LoanProgerss();

		    if (count($parts) > 0) {
                $loan->{$parts[0]} = $value;
//			    array_values( $parts )[0] = $value;
			    return $loan->getValue($parts[0], $loan);
		    } else {
			    return $model->attr;
		    }
    	}
    	
        if ($model->type == LoanExtra::TYPE) {
        	return $model->attr_to;
    	}
    	
    	if ($model->type == Person::TYPE) {
    		$person = new Person();
    		$person->{$model->attr} = $value;
    		return $person->getValue($model->attr, $person);
    	}
    	
    	return $value;
    }
    
    public static function getAttrText($model) {
    	if  (substr($model->attr, 0, 8) === "actions_") {
    		if  ($model->attr_from)  {
    			return "removed";
    		}
    		return "";
    	}

    	return self::getAttrValue($model, $model->attr_to)
	           ." <span class='pull-right changes-toggle glyphicon glyphicon-chevron-down' onclick='changeToggle(this);'>"
				."</span><span class='changes-from hide'>".self::getAttrValue($model, $model->attr_from)."</span>";
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
    	return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/changes', 'ID'),
            'type' => Yii::t('app/changes', 'Type'),
            'type_id' => Yii::t('app/changes', 'Type ID'),
            'attr' => Yii::t('app/changes', 'Attr'),
            'attr_from' => Yii::t('app/changes', 'Attr From'),
            'attr_to' => Yii::t('app/changes', 'Attr To'),
            'create_time' => Yii::t('app/changes', 'Create Time'),
        ];
    }
}
