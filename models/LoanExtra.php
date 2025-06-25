<?php

namespace app\models;

use Yii;
use app\components\Solr;

/**
 * This is the model class for table "loan_extra".
 *
 * @property string $id
 * @property string $loan_id
 * @property string $inbank_contract_id
 * @property string $property_address
 * @property string $car_description
 * @property string $car_phone
 * @property string $car_owner
 * @property string $car_owner_address
 * @property string $car_workplace
 * @property string $car_position
 * @property string $car_work_experience
 * @property string $vsaa_statement
 * @property string $car_owner_declared_address
 * @property string $credit_data
 * @property string $bank_account_statement
 * @property string $api_status_description
 * @property string $document_type
 * @property string $document_nr
 * @property string $document_expire
 * @property string $bank_account_nr
 * @property string $vsaa_statement_ep52
 *
 * @property Loan $loan
 */
class LoanExtra extends \yii\db\ActiveRecord
{
	/**
	 * Class type
	 * @var integer
	 */
	const TYPE = 3;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loan_extra';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loan_id'], 'required'],
            [['loan_id'], 'integer'],
            [['document_type', 'document_nr', 'document_expire', 'bank_account_nr', 'inbank_contract_id', 'property_address', 'car_owner', 'car_owner_address', 'car_workplace', 'car_position', 'car_work_experience'], 'string', 'max' => 256],
            [['car_description', 'vsaa_statement', 'bank_account_statement', 'api_status_description', 'vsaa_statement_ep52'], 'string', 'max' => 512],
            [['car_phone'], 'string', 'max' => 100],
            [['credit_data'], 'safe'],
        	[['car_owner_declared_address'], 'string', 'max' => 300],
        	[['loan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Loan::className(), 'targetAttribute' => ['loan_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/loan', 'ID'),
            'loan_id' => Yii::t('app/loan', 'Loan ID'),
            'property_address' => Yii::t('app/loan', 'Property Address'),
            'inbank_contract_id' => Yii::t('app/loan', 'Inbank contract ID'),
        	'car_description' => Yii::t('app/loan', 'Car Description'),
            'car_phone' => Yii::t('app/loan', 'Relative phone'),
            'car_owner' => Yii::t('app/loan', 'Relative fullname'),
            'car_owner_address' => Yii::t('app/loan', 'Address'),
            'car_workplace' => Yii::t('app/loan', 'Workplace'),
            'car_position' => Yii::t('app/loan', 'Position'),
            'car_work_experience' => Yii::t('app/loan', 'Work Experience'),
            'vsaa_statement' => Yii::t('app/loan', 'Vsaa Statement'),
            'credit_data' => Yii::t('app/loan', 'Credit data'),
            'car_owner_declared_address' => Yii::t('app/loan', 'Declared address'),
        	'bank_account_statement' => Yii::t('app/loan', 'Bank Account Statement'),
        		
        	'api_status_description' => Yii::t('app/loan', 'Inbank API Description'),
            'api_status_description_efinance' => Yii::t('app/loan', 'Efinance API Description'),
            'api_status_description_aizdevums' => Yii::t('app/loan', 'Aizdevums API Description'),
        		
        	'vsaa_statement_ep52' => Yii::t('app/loan', 'Vsaa Statement EP52'),
            'document_type' => Yii::t('app/loan', 'Document type'),
            'document_nr' => Yii::t('app/loan', 'Document nr'),
        	'document_expire' => Yii::t('app/loan', 'Document expire'),
            'bank_account_nr' => Yii::t('app/loan', 'Bank account nr'),
        ];
    }

    public function getAizdevumsDesc() {
    	$data = \Yii::$app->getDb()->createCommand("SELECT status_desc from aizdevums_api where loan_id=".$this->loan_id)->queryOne();
    	if ($data) {
    		return $data["status_desc"];
    	}
    }

    public function getEfinanceDesc() {
    	$data = \Yii::$app->getDb()->createCommand("SELECT status_desc from efinance_api where loan_id=".$this->loan_id)->queryOne();
    	if ($data) {
    		return $data["status_desc"];
    	}
    }
    
    /**
     * (non-PHPdoc)
     * @see \yii\db\BaseActiveRecord::save($runValidation, $attributeNames)
     */
    public function save($runValidation = true, $attributeNames = null) {
    	$history = [];
    	$history = $this->_parseFiles("bank_account_statement", $history);
    	$history = $this->_parseFiles("vsaa_statement", $history);
    	$history = $this->_parseFiles("vsaa_statement_ep52", $history);
    	
    	foreach ($this->getOldAttributes() as $oldAttrKey => $oldAttrValue) {
    		if ((string)$this->getAttribute($oldAttrKey) !== (string)$oldAttrValue) {
    			$history[] = [$oldAttrKey,$oldAttrValue,$this->getAttribute($oldAttrKey)];
    		}
    	}

    	if (parent::save($runValidation, $attributeNames)) {
    		foreach ($history as $change) {
    			Changes::setChanges(self::TYPE, $this->loan_id, $change[0], $change[1], $change[2]);
    		}
    		
    		$solr = new Solr();
    		$solr->setCollectionUrlByType(Loan::TYPE);
    		$solr->indexByModel($this->loan);
    		
    		return true;
    	}
    
    	return false;
    }
    
    private function _parseFiles($name, $history) {
    	if (is_array($this->getAttribute($name))) {
    		$lastFile = $this->getOldAttribute($name);
    		$i = 0;
    		foreach ($this->getAttribute($name) as $x => $file) {
    			$i++;
    			 
    			if ($i !== count($this->getAttribute($name))) {
    				$history[] = [$name, null, $file];
    			}
    			 
    			$lastFile = $file;
    		}
    		$this->setAttribute($name, $lastFile);
    	}
    	
    	return $history;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
        return $this->hasOne(Loan::className(), ['id' => 'loan_id']);
    }
}
