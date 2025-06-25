<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "loan_progerss".
 *
 * @property string $id
 * @property string $loan_id
 * @property integer $provider_id
 * @property integer $status
 * @property string $text
 *
 * @property Loan $loan
 */
class LoanProgerss extends \yii\db\ActiveRecord
{
	/**
	 * Class type
	 * @var integer
	 */
	const TYPE = 5;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loan_progerss';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loan_id', 'provider_id', 'status'], 'required'],
            [['loan_id', 'provider_id', 'status', 'lead_status'], 'integer'],
            [['text', 'amount', 'api_response_id'], 'string'],
            [['loan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Loan::className(), 'targetAttribute' => ['loan_id' => 'id']],
        ];
    }

    public function getBarClass($class = '') {
        if ($this->status == 1) {
            return " " . $class . " in-progress btn-warning";
        }
        if ($this->status == 2) {
            return " " . $class . " reject btn-danger";
        }
        if ($this->status == 3) {
            return " " . $class . " accept btn-success";
        }

        if ($this->status == 4) {
            return " " . $class . " accept btn-success";
        }
        return " " . $class . "btn-default";
    }
    
    public static function getStatuses() {
    	return [
    			0 => "-",
    			1 => Yii::t( "app/loan","In progress"),
    			2 => Yii::t( "app/loan","Rejected"),
    			3 => Yii::t( "app/loan","Accepted"),
                4 => Yii::t( "app/loan","Paid"),
		];
    }

    /**
     * (non-PHPdoc)
     * @see \yii\db\BaseActiveRecord::save($runValidation, $attributeNames)
     */
    public function save($runValidation = true, $attributeNames = null) {
    	$history = [];

    	foreach ($this->getOldAttributes() as $oldAttrKey => $oldAttrValue) {
    		if ((string)$this->getAttribute($oldAttrKey) !== (string)$oldAttrValue) {
    			$history[] = [$oldAttrKey."-".$this->provider_id,$oldAttrValue,$this->getAttribute($oldAttrKey)];
    		}
    	}
    	
    	if ($this->isNewRecord) {
    		Changes::setChanges(self::TYPE, $this->loan_id, "text-".$this->provider_id, null, $this->text);
    		Changes::setChanges(self::TYPE, $this->loan_id, "status-".$this->provider_id, null, $this->status);
    		if ($this->amount) {
    			Changes::setChanges(self::TYPE, $this->loan_id, "amount-".$this->provider_id, null, $this->amount);
    		}
    	}
    	
    	if (parent::save($runValidation, $attributeNames)) {
    		foreach ($history as $change) {
    			Changes::setChanges(self::TYPE, $this->loan_id, $change[0], $change[1], $change[2]);
    		}

    		return true;
    	}
    
    	return false;
    }
    public function getValue($name, $model) {
    	switch ($name) {
    		case "status":
    			return $this->getStatuses()[(int)$model->$name];
    			break;
    		case "provider":
    			return $this->getStatuses()[(int)$model->$name];
    			break;
    		default:
    			return $model->$name;
    	}
    }
    
    public function getAttributeLabelFormated($attr) {
    	$parts = explode("-", $attr);
    	$progress = new Loan();
    	
    	$txt = (isset($progress->getInProgress()[$parts[1]]) ? $progress->getInProgress()[$parts[1]] : null)." - ";
    	$txt .= $this->getAttributeLabel($parts[0]);
    	 
    	return $txt;
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/loan', 'Progress ID'),
            'loan_id' => Yii::t('app/loan', 'Progress Loan ID'),
            'provider_id' => Yii::t('app/loan', 'Progress Provider ID'),
        		
        		'status' => Yii::t('app/loan', 'Progress Status'),
            'amount' => Yii::t('app/loan', 'Progress Amount'),
        		'text' => Yii::t('app/loan', 'Progress Text'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
        return $this->hasOne(Loan::className(), ['id' => 'loan_id']);
    }
}
