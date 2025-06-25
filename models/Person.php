<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\components\Solr;

/**
 * This is the model class for table "{{%person}}".
 *
 * @property string $id
 * @property string $name
 * @property string $surname
 * @property string $personal_code
 * @property integer $income
 * @property integer $outcome
 * @property integer $dependants
 * @property integer $accept_email
 * @property string $phone
 * @property string $email
 * @property integer $credit_history
 * @property integer $loan_count
 * @property integer $unique_id
 * @property string $description
 * @property string $workplace
 * @property string $gender
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property Loan[] $loans
 * @property Loan $loan
 */
class Person extends \yii\db\ActiveRecord
{
	public $loan_count_period;
	
	/**
	 * Class type
	 * @var integer
	 */
	const TYPE = 1;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%person}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['name', 'surname', 'personal_code', 'income', 'phone', 'email'], 'required'],
            [[ 'credit_history', 'create_time', 'update_time', 'loan_count', 'family_status', 'education', 'length_of_service', 'unique_id'], 'integer'],
            [['description', 'gender', 'workplace', 'position', 'privacy', 'address', 'bank_name', 'city', 'post_code'], 'string'],
        	[['income', 'outcome', 'salary'], 'double'],
            [['name', 'surname', 'email','marital_status','company_name','registration_number','company_duration_months','company_turnover','dependants'], 'string', 'max' => 250],
            [['personal_code', 'phone', 'accept_email', 'annual_turnover', 'working_time', 'town', 'realEstate'], 'safe'],
        	//[['personal_code'], 'unique'],
        	//[['personal_code'], 'trim'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
    	$modules = new \app\components\modules\ModulesInit();
    	return $modules->setBehavior([
    			[
    					'class' => TimestampBehavior::className(),
    					'createdAtAttribute' => 'create_time',
    					'updatedAtAttribute' => 'update_time',
    					'value' => time(),
    			],
    	], "person");
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/person', 'ID'),
            'name' => Yii::t('app/person', 'Name'),
            'surname' => Yii::t('app/person', 'Lastname'),
            'gender' => Yii::t('app/person', 'Gender'),
        	'personal_code' => Yii::t('app/person', 'Personal Code'),
            'income' => Yii::t('app/person', 'Income'),
            'outcome' => Yii::t('app/person', 'Outcome'),
            'dependants' => Yii::t('app/person', 'Dependants'),
            'phone' => Yii::t('app/person', 'Phone'),
            'email' => Yii::t('app/person', 'Email'),
            'credit_history' => Yii::t('app/person', 'Inbank status'),
            'loan_count' => Yii::t('app/person', 'Loan count'),
            'loan_count_period' => Yii::t('app/person', 'Loan count 6 months'),
            'family_status' => Yii::t('app/person', 'Family status'),
            'education' => Yii::t('app/person', 'Education'),
        	'description' => Yii::t('app/person', 'Description'),
            'create_time' => Yii::t('app/person', 'Create date'),
            'update_time' => Yii::t('app/person', 'Update date'),
            'accept_email' => Yii::t('app/person', 'Accept Email'),
            'workplace' => Yii::t('app/person', 'Workplace'),
            'position' => Yii::t('app/person', 'Position'),
            'length_of_service' => Yii::t('app/person', 'Length Of Service'),
            'company_name' => Yii::t('app/person', 'Company Name'),
             'company_turnover'      => strtoupper(Yii::t('app/person', 'Company Turnover')),
             'company_duration_months' => strtoupper(Yii::t('app/person', 'Company Work Month')),
             'registration_number'   => strtoupper(Yii::t('app/person', 'Registration Number')),
               'company_length_of_service' => strtoupper(Yii::t('app/person', 'Company in operation')),
            'privacy' => Yii::t('app/person', 'Privacy'),
            'address' => Yii::t('app/person', 'Full declared address'),
            'salary' => Yii::t('app/person', 'Salary'),
            'annual_turnover' => Yii::t('app/person', 'Annual Turnover'),
            'working_time' => Yii::t('app/person', 'Working Time'),
            'town' => Yii::t('app/person', 'Town'),
            'realEstate' => Yii::t('app/person', 'Real estate'),
            'marital_status'=>Yii::t('app/person', 'Marital Status'),
            'city'=>Yii::t('app/person', 'city'),
        ];
    }

    /**
     * Get credit history
     * @return array
     */
    public function getCreditHistory() {
    	return [
    			"0" => Yii::t("app/loan", "-"),
    			"1" => Yii::t("app/loan", "Positive"),
    			"2" => Yii::t("app/loan", "Negative"),
    			"3" => Yii::t("app/loan", "Manual"),
    			"4" => Yii::t("app/loan", "Error"),
    	];
    }

    /**
     * Get credit history
     * @return array
     */
    public function getFamilyStatus() {
    	return [
	    		0 => "Neprecējies/Neprecējusies",
	    		1 => "Faktiskā kopdzīve",
	    		2 => "Precējies/Precējusies",
	    		3 => "Šķīries/Šķīrusies",
	    		4 => "Atraitnis/Atraitne",
	    ];
    }

    /**
     * Get credit history
     * @return array
     */
    public function getEducation() {
    	return [
	    		0 => "Pamata izglītība",
	    		1 => "Vidējā izglītība",
	    		2 => "Vidējā speciālā",
	    		3 => "Augstākā izglītība",
	    ];
    }

    public function getValue($name, $model) {
    	switch ($name) {
    		case "credit_history":
    			return $this->getCreditHistory()[$model->$name];
    			break;
    		case "income":
    			return $model->$name." €";
    			break;
    		case "outcome":
    			return $model->$name." €";
    			break;
    		case "family_status":
    			return isset($this->getFamilyStatus()[$model->$name]) ? $this->getFamilyStatus()[$model->$name] : null;
    			break;
    		case "loan_count_period":
    			return $model->getLoans()->where(["between", "create_time", strtotime("-6 months"), time()])->count() ;
    			break;
    		case "education":
    			return isset($this->getEducation()[$model->$name]) ? $this->getEducation()[$model->$name] : null;
    			break;
    		case "accept_email":
    			return ($model->accept_email ? "Yes" : "No");
    			break;
    		case "loan.referral":
    			return ($model ? $model->referral : null);
    			break;
    		default:
    			return $model->$name;
    	}
    }
    
    /**
     * (non-PHPdoc)
     * @see \yii\db\BaseActiveRecord::save($runValidation, $attributeNames)
     */
    public function save($runValidation = true, $attributeNames = null) {
    	$history = [];
    	foreach ($this->getOldAttributes() as $oldAttrKey => $oldAttrValue) {
    		if ((string)$this->getAttribute($oldAttrKey) !== (string)$oldAttrValue) {
    			$history[] = [$oldAttrKey,$oldAttrValue,$this->getAttribute($oldAttrKey)];
    		}
    	}
    	 
    	if (parent::save($runValidation, $attributeNames)) {
    		foreach ($history as $change) {
    			Changes::setChanges(self::TYPE, $this->id, $change[0], $change[1], $change[2]);
    		}
    		
    		$solr = new Solr();
			$solr->setCollectionUrlByType(self::TYPE);
    		$solr->indexByModel($this);
    		
    		return true;
    	}
    
    	return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoans()
    {
        return $this->hasMany(Loan::className(), ['person_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
        return $this->hasOne(Loan::className(), ['person_id' => 'id']);
        
    }
}
