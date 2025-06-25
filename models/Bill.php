<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\components\Solr;

/**
 * This is the model class for table "bill".
 *
 * @property string $id
 * @property string $loan_id
 * @property double $amount
 * @property integer $status
 * @property integer $type
 * @property string $create_time
 * @property string $update_time
 * @property string $due_time
 * @property string $close_time
 *
 * @property Loan $loan
 */
class Bill extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bill';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loan_id', 'amount', 'type'], 'required'],
            [['loan_id', 'status', 'type'], 'integer'],
        	[['create_time', 'update_time', 'close_time', 'due_time'], 'safe'],
            [['amount'], 'number'],
            [['loan_id'], 'unique'],
        	[['loan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Loan::className(), 'targetAttribute' => ['loan_id' => 'id']],
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
    					'value' => time(),
    			],
    	];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/bill', 'ID'),
            'loan_id' => Yii::t('app/bill', 'Loan ID/Bill ID'),
            'amount' => Yii::t('app/bill', 'Amount'),
            'status' => Yii::t('app/bill', 'Status'),
            'type' => Yii::t('app/bill', 'Type'),
            'create_time' => Yii::t('app/bill', 'Create date'),
            'update_time' => Yii::t('app/bill', 'Update date'),
            'close_time' => Yii::t('app/bill', 'Paid date'),
            'due_time' => Yii::t('app/bill', 'Due date'),
        ];
    }

    public function getTypes() {
    	return [
    			"1" => "Standart",	
    			"2" => "Courier",	
    	];
    }

    public function getStatuses() {
    	return [
    			"0" => "Issued",	
    			//"1" => "Signed",	
    			"2" => "Paid",
    			"3" => "Debt",
    	];
    }

    /**
     * (non-PHPdoc)
     * @see \yii\db\BaseActiveRecord::beforeValidate()
     */
    public function beforeValidate() {
    	if (!$this->close_time) {
    		if ($this->status == 2) {
    			if ($this->isAttributeChanged("status")) {
    				$this->close_time = time();
    			}
    		}
    	}
    	
    	return parent::beforeValidate();
    }
    
    public function getValue($name, $model) {
    	switch ($name) {
    		case "status":
    			return $this->getStatuses()[(int)$model->$name];
    			break;
    		case "type":
    			return $this->getTypes()[(int)$model->$name];
    			break;
    		default:
    			return $model->$name;
    	}
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
        return $this->hasOne(Loan::className(), ['id' => 'loan_id']);
    }
    
    /**
     * (non-PHPdoc)
     * @see \yii\db\BaseActiveRecord::save($runValidation, $attributeNames)
     */
    public function save($runValidation = true, $attributeNames = null) {
    	if (parent::save($runValidation, $attributeNames)) {
    		$solr = new Solr();
    		$solr->setCollectionUrlByType(Loan::TYPE);
    		$solr->indexByModel($this->loan);
    			
    		return true;
    	}
    
    	return false;
    }
}
