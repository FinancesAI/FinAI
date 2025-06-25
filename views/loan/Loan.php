<?php

namespace app\models;

use app\components\modules\LoanTargetCircleBehavior;
use app\components\Solr;
use app\controllers\MailController;
use app\services\SourceService;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%loan}}".
 *
 * @property string $id
 * @property string $unique_id
 * @property string $person_id
 * @property string $user_id
 * @property integer $amount
 * @property integer $term
 * @property integer $status
 * @property string $source
 * @property integer $product
 * @property integer $deal_stage
 * @property integer $deal_product
 * @property string $referral
 * @property string $query_string
 * @property string $ip_ountry
 * @property string $lang
 * @property array $actions
 * @property string $description
 * @property string $cid
 * @property string $description_2
 * @property string $last_changed_field
 * @property string $rating
 * @property string $revenue
 * @property string $courier
 * @property string $realEstate
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $close_time
 * @property integer $reminder_time
 * @property integer $reminder_class
 * @property integer $waiting_time
 * @property integer $approved
 * @property integer $first_payment
 * @property integer $is_tc_sent
 * @property integer $acceptance
 *
 * @property Person $person
 * @property LoanExtra $extra
 * @property LoanEnterprise $enterprise
 * @property LoanGuarantor $guarantor
 * @property LoanProgerss $progress
 * @property Bill $bill
 * @property bool $need_reindex [tinyint(1)]
 * @property string $ceo [varchar(250)]
 * @property string $Zvanits [varchar(250)]
 * @property int $color_id [int(11)]
 */
class Loan extends \yii\db\ActiveRecord {
	public $bill_amount;
	public $bill_status;
	public $prog;
	public $coe;
	public $ratingText;

	/**
	 * Class type
	 * @var integer
	 */
	const TYPE = 2;

	const STATUS_IN_PROGRES = 1;

	const STATUS_REJECTED = 4;

	const STATUS_REFUSES = 7;

	const STATUS_CLOSED = 5;

	const STATUS_NEW = 0;

	public $actionsBefore = [];

	public $skipReminder = false;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%loan}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
//            [['amount', 'term', 'person_id'], 'required'],
			[
				[   
					'id',
					'person_id',
					'user_id',
					'term',
					'approved',
					'status',
					'product',
					'deal_stage',
					'deal_product',
					'create_time',
					'update_time',
					'reminder_time',
					'close_time',
					'waiting_time',
					'year',
					'source_id',
					'company_length_of_service',
					'courier',
                    'acceptance',
                    'true_beneficiary',
                    'politically_significant',
                  
                    'invoice_amount',
  					'loan_type',
                    'agree'
				],
				'integer'
			],
			[
				[
					'description',
					'unique_id',
					'lang',
					'color_id',
					'autoregnr',
					'techpass',
					'property_address',
					'type',
					
					'invoice_due_date',
					'document',
					'revenue',
                    'realEstate',
                 	'car_brand',
                 	'car_model',
                    'property_type',
                    'loan_purpose',
                    'car_ad_link',
                    'invoice_due_date',
                    'year_of_issue',
                    'owned_transport'
				],
				'string'
			],
			[ [ 'actions', 'lang', 'reminder_class', 'pledge', 'tmt_data', 'source' ], 'safe' ],
			[
				[ 'referral', 'query_string', 'cid', 'description_2', 'last_changed_field', 'rating', 'is_tc_sent' ],
				'safe'
			],
			[ [ 'amount', 'first_payment', 'price', 'amount_taken','step_monthly_payment' ,'total_credit_balance','additional_credit_amount'], 'double' ],
			[ [ 'ip_ountry' ], 'string', 'max' => 250 ],
			//[['person_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::className(), 'targetAttribute' => ['person_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			LoanTargetCircleBehavior::className(),
            [
                'class'              => TimestampBehavior::class,
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value'              => time(),
            ],
		];

//		$modules = new \app\components\modules\ModulesInit();
//    	return $modules->setBehavior([
//    			[
//    					'class' => TimestampBehavior::className(),
//    					'createdAtAttribute' => 'create_time',
//    					'updatedAtAttribute' => 'update_time',
//    					'value' => time(),
//    			],
//    	], "loan");
	}

	/**
	 * @inheritdoc
	 */
public function attributeLabels() {
    return [
        'id'     => strtoupper(Yii::t('app/loan', 'ID')),
        'person_email'     => strtoupper(Yii::t('app/loan', 'Person Email')),
        'person_phone'     => strtoupper(Yii::t('app/loan', 'Person Phone')),
        'person_personal_code'     => strtoupper(Yii::t('app/loan', 'Person Personal Code')),
        'person_name'     => strtoupper(Yii::t('app/loan', 'Person Name')),
        'person_surname'     => strtoupper(Yii::t('app/loan', 'Person Surname')),
        'person_loan_count'     => strtoupper(Yii::t('app/loan', 'PLC')),
        'unique_id'             => strtoupper(Yii::t('app/loan', 'Unique ID')),
        'person_id'             => strtoupper(Yii::t('app/loan', 'Person ID')),
        'user_id'               => strtoupper(Yii::t('app/loan', 'User')),
        'amount'                => strtoupper(Yii::t('app/loan', 'Loan amount')),
        'approved'              => strtoupper(Yii::t('app/loan', 'In progress')),
        'first_payment'         => strtoupper(Yii::t('app/loan', 'First payment')),
        'term'                  => strtoupper(Yii::t('app/loan', 'Term')),
        'status'                => strtoupper(Yii::t('app/loan', 'Status')),
        'source'                => strtoupper(Yii::t('app/loan', 'Source')),
        'product'               => strtoupper(Yii::t('app/loan', 'Product')),
        'deal_stage'            => strtoupper(Yii::t('app/loan', 'Deal Stage')),
        'deal_product'          => strtoupper(Yii::t('app/loan', 'Deal product')),
        'referral'              => strtoupper(Yii::t('app/loan', 'Referral')),
        'query_string'          => strtoupper(Yii::t('app/loan', 'Query String')),
        'ip_ountry'             => strtoupper(Yii::t('app/loan', 'Ip Ountry')),
        'description'           => strtoupper(Yii::t('app/loan', 'Description')),
        'create_time'           => strtoupper(Yii::t('app/loan', 'Create date')),
        'update_time'           => strtoupper(Yii::t('app/loan', 'Update date')),
        'close_time'            => strtoupper(Yii::t('app/loan', 'Close date')),
        'person_credit_history' => strtoupper(Yii::t('app/loan', 'Inbank status')),
        'reminder_time'         => strtoupper(Yii::t('app/loan', 'Reminder date')),
        'waiting_time'          => strtoupper(Yii::t('app/loan', 'Waiting Time')),
        'actions'               => strtoupper(Yii::t('app/loan', 'Actions')),
        'lang'                  => strtoupper(Yii::t('app/loan', 'Language')),
        'prog'                  => strtoupper(Yii::t('app/loan', 'Progress')),
        'description_2'         => strtoupper(Yii::t('app/loan', 'Description 2')),
        'last_changed_field'    => strtoupper(Yii::t('app/loan', 'Last changed field')),
        'rating'                => strtoupper(Yii::t('app/loan', 'Rating')),
        'company_name'          => strtoupper(Yii::t('app/loan', 'Company Name')),
        'year'                  => strtoupper(Yii::t('app/loan', 'Year')),
        'autoregnr'             => strtoupper(Yii::t('app/loan', 'Autoregnr')),
        'techpass'              => strtoupper(Yii::t('app/loan', 'Techpass')),
        'pledge'                => strtoupper(Yii::t('app/loan', 'pledge')),
        'property_address'      => strtoupper(Yii::t('app/loan', 'property_address')),
        'price'                 => strtoupper(Yii::t('app/loan', 'price')),
        'amount_taken'          => strtoupper(Yii::t('app/loan', 'amount_taken')),
        'realEstate'            => strtoupper(Yii::t('app/loan', 'Real Estate')),
        'revenue'               => strtoupper(Yii::t('app/loan', 'Revenue')),
      
        'acceptance'            => strtoupper(Yii::t('app/loan', 'Accepted loans')),
        'loan_purpose'          => strtoupper(Yii::t('app/loan', 'Loan Purpose')),
        'property_type'         => strtoupper(Yii::t('app/loan', 'Property Type')),
        'owned_transport'       => strtoupper(Yii::t('app/loan', 'What kind of transport do you own?')),
        'car_ad_link' => strtoupper(Yii::t('app/loan','Link to car advertisement')),
        'total_credit_balance'  => strtoupper(Yii::t('app/loan', 'Total credit balance')),
        'additional_credit_amount' => strtoupper(Yii::t('app/loan', 'Additional credit amount')),
       'company_duration_months'=> strtoupper(Yii::t('app/loan', 'Company Operating Duration')),
        
        'step_monthly_payment'  => strtoupper(Yii::t('app/loan', 'Step Monthly Payment')),
        'invoice_amount'        => strtoupper(Yii::t('app/loan', 'Invoice Amount')),
        'invoice_due_date'      => strtoupper(Yii::t('app/loan', 'Invoice due date')),
           'car_model'      => strtoupper(Yii::t('app/loan', 'Car model')),
              'car_brand'      => strtoupper(Yii::t('app/loan', 'Car brand')),
        
        'year_of_issue'      => strtoupper(Yii::t('app/loan', 'Year of issue')),
    ];
}


	public function getWaitingClassBankLoans() {
		$class       = '';
		$provider_id = Yii::$app->user->identity->provider_id;

		$loanProgress = LoanProgerss::findOne( [ 'provider_id' => $provider_id, 'loan_id' => $this->id ] );
		if ( $loanProgress != null ) {
            $class = $loanProgress->getBarClass("bank-loans");
        }
//		} else {
//            if ( $this->status == 1 ) {
//                $class = "bank-loans in-progress btn-warning" . $this->getReminderClass();
//            }
//
//            if ( $this->status == 3 ) {
//                $class = "bank-loans accept btn-success" . $this->getReminderClass();
//            }
//
//            if ( $this->status == 4 ) {
//                $class = "bank-loans reject btn-danger" . $this->getReminderClass();
//            }
//        }

		return $class;
	}

	/**
	 * Get waiting time class
	 * @return string
	 */
	public function getWaitingClass() {
		$class = "";

		if ( strlen( $this->getLoanCSSColor() ) > 0 ) {
			$class = "inherit";

			return $class . $this->getReminderClass();
		}

		if ( $this->rating == 1 ) {
			return " positive positive-rating" . $this->getReminderClass();
		}

		if ( $this->rating == - 1 ) {
			return " negative danger negative-rating" . $this->getReminderClass();
		}

		if ( $this->status == 1 && $this->person->credit_history == 1 ) {
			return " positive" . $this->getReminderClass();
		}

		if ( $this->status !== 0 ) {
			return $class . $this->getReminderClass();
		}

		if ( $this->waiting_time >= Yii::$app->setting->get( "waiting_time_info" ) ) {
			$class = "info";
		}

		if ( $this->waiting_time >= Yii::$app->setting->get( "waiting_time_warning" ) ) {
			$class = "warning";
		}

		if ( $this->waiting_time >= Yii::$app->setting->get( "waiting_time_danger" ) ) {
			$class = "danger";
		}

		if ( $this->person->credit_history == 1 ) {
			$class .= " positive";
		}

		return $class . $this->getReminderClass();
	}

	public function getColorInheritClass() {
		if ( strlen( $this->getLoanCSSColor() ) > 0 ) {
			return ' inherit';
		}

		return '';
	}

	public function getReminderClass() {
		if ( ! $this->reminder_class ) {
			return "";
		}

		return " reminder-" . $this->reminder_class;
	}

    /**
     * Get approved
     * Use variable approved $this->approved
     * @return array
     */
    public function getInProgress(): array
    {
        $providers = Provider::find()
            ->where(['enable' => 1])
            ->orderBy('id')
            ->all();

        $inProgress = [
            '0' => Yii::t('app/loan', '-')
        ];

        foreach ($providers as $provider) {
            $inProgress[$provider->id] = Yii::t('app/loan', $provider->name);
        }

        return $inProgress;
    }

	/**
     * @deprecated
     *
	 * Get approved
	 * Use variable approved $this->approved
	 * @return array
	 */
	public function oldGetInProgress() {
		return [
			'0'  => Yii::t( 'app/loan', '-' ),
			'1'  => Yii::t( 'app/loan', 'Monify' ),
//			'4'  => Yii::t( 'app/loan', 'Finanza' ),
			'6'  => Yii::t( 'app/loan', 'TF Bank' ),
			'9'  => Yii::t( 'app/loan', 'Latvijas hipotēka' ),
			'10' => Yii::t( 'app/loan', 'Sauleskredīts' ),
			'11' => Yii::t( 'app/loan', 'Nordic Finance' ),
			'15' => Yii::t( 'app/loan', 'Square+' ),
			'16' => Yii::t( 'app/loan', 'Monenza' ),
            '17' => Yii::t( 'app/loan', 'Nord Card' ),
            '18' => Yii::t( 'app/loan', 'Ūnijas Auto' ),
//            '19' => Yii::t( 'app/loan', 'Inbank' ),
            '20' => Yii::t( 'app/loan', 'AUTOLIZING.LV' ),
            '22' => Yii::t( 'app/loan', 'NORD Līzings' ),
            '23' => Yii::t( 'app/loan', 'MOGO' ),
		];
	}

	public function getProgressFormated() {
		$formated  = [];
		$progesses = $this->progress;
		foreach ( $progesses as $progress ) {
			$formated[ $progress->provider_id ] = $progress;
		}

		return $formated;
	}

	/**
	 * Get statuses
	 * @return array
	 */
	public static function getStatuses() {
		return [
            "0"  => Yii::t( "app/loan", "New"),
            "1"  => Yii::t( "app/loan", "In progress" ),
            "2"  => Yii::t( "app/loan", "Waiting extra info" ),
            "3"  => Yii::t( "app/loan", "Approved" ),
            "4"  => Yii::t( "app/loan", "Rejected" ),
            "15" => Yii::t( "app/loan", "Сustomer declined" ),
            "5"  => Yii::t( "app/loan", "Paid out" ),
            "12" => Yii::t( "app/loan", "Keep working" ),
            "11" => Yii::t( "app/loan", "Test" ),
            "13" => Yii::t( "app/loan", "Approved amount" ),
            "14" => Yii::t( "app/loan", "Issued amount" ),
            "7"  => Yii::t( "app/loan", "Refuses" ),
		];
	}

	/**
	 * Get products
	 * @return array
	 */
	public function getProducts() {
		return [
			"0" => Yii::t( "app/loan", "-" ),
			"1" => Yii::t( "app/loan", "credit" ),
            "10" => Yii::t( "app/loan", "Insurance" ),
            "11" => Yii::t( "app/loan", "Deposits" ),
		];
	}

	/**
	 * Get deal stages
	 * @return array
	 */
	public function getDealStages() {
		return [
			"0"  => Yii::t( "app/loan", "-" ),
			"1"  => Yii::t( "app/loan", "Inbank won" ),
			"2"  => Yii::t( "app/loan", "Uno won" ),
			"3"  => Yii::t( "app/loan", "Mogo won" ),
			"12" => Yii::t( "app/loan", "E-lats won" ),
			"4"  => Yii::t( "app/loan", "Vitacredit won" ),
			"11" => Yii::t( "app/loan", "TFBank won" ),
			//"5" => Yii::t("app/loan", "Motoro won"),
			"6"  => Yii::t( "app/loan", "Iespējukredīts.lv won" ),
			"7"  => Yii::t( "app/loan", "Credico won" ),
			"9"  => Yii::t( "app/loan", "Bigbank won" ),
			"8"  => Yii::t( "app/loan", "Credit24 won" ),
			"13" => Yii::t( "app/loan", "Citadele won" ),
			"10" => Yii::t( "app/loan", "Canceled" ),
		];
	}

	/**
	 * Get actions
	 * @return array
	 */
	public function getActions() {
		return [
			"61" => Yii::t( "app/loan", "Sazvanīts" ),// used
			"62" => Yii::t( "app/loan", "Call made, no answer x1" ), // no need
			"63" => Yii::t( "app/loan", "Call made, no answer x2" ), // no need
			"70" => Yii::t( "app/loan", "Call made, no answer x3" ), // no need
			"64" => Yii::t( "app/loan", "Nav atbildes" ), // used
			"65" => Yii::t( "app/loan", "Waiting for agreement(courier) data" ), // no need
			"66" => Yii::t( "app/loan", "Courier needed" ),
			"67" => Yii::t( "app/loan", "Courier sent" ), // no need
			"68" => Yii::t( "app/loan", "Need time to think over" ), // no need
			"69" => Yii::t( "app/loan", "Refuses from loan" ), // used
			"70" => Yii::t( "app/loan", "Nav oficiālas darbavietas" ),// used
			"71" => Yii::t( "app/loan", "Atrodas dekrētā" ),// used

			"80" => Yii::t( "app/loan", "Viewed emailed link" ),
			"81" => Yii::t( "app/loan", "Updated emailed link" ),

			"97" => Yii::t( "app/loan", "Bill deleted" ),
			"98" => Yii::t( "app/loan", "Sent file to inbank" ),
			"99" => Yii::t( "app/loan", "Bill generated" ),

			"01" => Yii::t( "app/loan", "Sent email VSAA izziņa par ienākumiem" ),
			"02" => Yii::t( "app/loan", "Sent email papildus dati" ),
			"03" => Yii::t( "app/loan", "Sent email papildus informācija kredītu apvienošanai" ),
			"04" => Yii::t( "app/loan", "Sent email bankas konta izraksts" ),
			"05" => Yii::t( "app/loan", "Sent email aizdevums ir apstiprināts" ),
			"06" => Yii::t( "app/loan", "Sent email informācija līgumam" ),
			"07" => Yii::t( "app/loan", "Sent email VSAA izziņa par pensijām/pabalstiem/atlīdzību" ),
			"08" => Yii::t( "app/loan", "Sent email papildus informācija auto līzingam" ),
			"09" => Yii::t( "app/loan", "Sent email custom text" ),
			"10" => Yii::t( "app/loan", "Sent email galvotāja informācija" ),
			"11" => Yii::t( "app/loan", "Sent email e signature" ),
			"12" => Yii::t( "app/loan", "Sent email bill reminder info" ),
			"13" => Yii::t( "app/loan", "Sent email bill reminder alert" ),
			"14" => Yii::t( "app/loan", "Sent email informācija par uzņēmumu" ),
			"15" => Yii::t( "app/loan", "Sent email VSAA un Bankas pārskats" ),
			"16" => Yii::t( "app/loan", "Sent email Izziņas par Taviem kredītu atlikumiem" ),
			"17" => Yii::t( "app/loan", "Sent email ārzemēs strādājošajiem" ),
			"19" => Yii::t( "app/loan", "Sent email rejected" ),
			"20" => Yii::t( "app/loan", "Sent email debt collection company" ),
			"21" => Yii::t( "app/loan", "Sent email external form" ),
			"25" => Yii::t( "app/loan", "Sent email auto reject" ),

			"31" => Yii::t( "app/loan", "Sent sms Reject loan" ),
			"32" => Yii::t( "app/loan", "Sent sms Information sent to email" ),
			"33" => Yii::t( "app/loan", "Sent sms Send address" ),
			"34" => Yii::t( "app/loan", "Sent sms custom text" ),
			"35" => Yii::t( "app/loan", "Sent sms bill reminder info" ),
			"36" => Yii::t( "app/loan", "Sent sms bill reminder alert" ),
			"37" => Yii::t( "app/loan", "Sent sms contact request" ),
			"38" => Yii::t( "app/loan", "Sent sms debt collection company" ),
			"39" => Yii::t( "app/loan", "Sent sms accept, contact us" ),

			"40" => Yii::t( "app/loan", "Sent iespeja email api" ),
			"41" => Yii::t( "app/loan", "Sent e-lats email api" ),
		];
	}

	/**
	 * Get deal stages product
	 *
	 * @param string $clean
	 *
	 * @return array
	 */
	public function getDealStageProduct( $clean = false ) {
		$array = [
			"0"  => [],
			"1"  => [ // Inbank
				[
					"id"    => "1",
					"title" => "Auto izmaksa",
				],
				[
					"id"    => "2",
					"title" => "Akcija auto izmaksa",
				],
				[
					"id"    => "3",
					"title" => "Patēriņa kredīts standart",
				],
				[
					"id"    => "4",
					"title" => "Patēriņa kredīts high",
				],
				[
					"id"    => "5",
					"title" => "Patēriņa kredīts premium",
				],
			],
			"2"  => [ // uno
				[
					"id"    => "6",
					"title" => "Naudas kredīts 38%",
				],
				[
					"id"    => "7",
					"title" => "Naudas kredīts 35%",
				],
				[
					"id"    => "8",
					"title" => "Līzings lētais",
				],
				[
					"id"    => "9",
					"title" => "Uno līzings",
				],
			],
			"3"  => [ // Mogo
				[
					"id"    => "10",
					"title" => "Auto līzings",
				],
				[
					"id"    => "11",
					"title" => "Kredīts pret auto ķīlu",
				],
			],
			"4"  => [ // Vitacredit
				[
					"id"    => "12",
					"title" => "Auto līzings",
				],
//     					[
//     							"id" => "13",
//     							"title" => "Kredīts pret auto ķīlu",
//     					],
			],
			"5"  => [ // Motoro
				[
					"id"    => "14",
					"title" => "Auto līzings",
				],
				[
					"id"    => "15",
					"title" => "Kredīts pret auto ķīlu",
				],
			],
			"6"  => [ //iespējukredīts.lv
				[
					"id"    => "16",
					"title" => "Naudas kredīts",
				],
			],
			"7"  => [ //Credico
				[
					"id"    => "17",
					"title" => "Naudas kredīts",
				],
				[
					"id"    => "18",
					"title" => "Kredītu apvienošana",
				],
				[
					"id"    => "19",
					"title" => "Auto līzings",
				],
			],
			"8"  => [ //credit24.lv
				[
					"id"    => "20",
					"title" => "Naudas kredīts",
				],
			],
			"9"  => [ //bigbank.lv
				[
					"id"    => "21",
					"title" => "Auto 10%",
				],
				[
					"id"    => "22",
					"title" => "Auto 11%+",
				],
				[
					"id"    => "23",
					"title" => "Nauda 19%",
				],
				[
					"id"    => "24",
					"title" => "Nauda 29%",
				],
				[
					"id"    => "25",
					"title" => "Apvienošana 45%",
				],
			],
			"11" => [ //tfbank.lv
				[
					"id"    => "26",
					"title" => "Naudas kredīts",
				],
				[
					"id"    => "29",
					"title" => "Auto līzings",
				],
			],
			"12" => [ //e lats
				[
					"id"    => "27",
					"title" => "Auto līzings",
				],
			],
			"13" => [ //citadele
				[
					"id"    => "28",
					"title" => "Naudas kredīts",
				],
			],
		];
		if ( $clean ) {
			$cleanArray    = [];
			$cleanArray[0] = [ "id" => "0", "title" => "-" ];
			foreach ( $array as $key => $value ) {
				foreach ( $value as $k => $v ) {
					$id                = @$v["id"];
					$cleanArray[ $id ] = $v;
				}
			}

			return $cleanArray;
		}

		return $array;
	}

	/**
	 * Get user fullname
	 * @return string
	 */
	public function getUser( $userId ) {
		if ( $userId ) {
			$user = User::find()->where( [ "id" => $userId ] )->one();
			if ( $user ) {
				return $user->fullname;
			}
		}

		return null;
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getStatus() {
		return $this->hasOne( LoanStatus::className(), [ 'id' => 'status' ] );
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPerson() {
		return $this->hasOne( Person::className(), [ 'id' => 'person_id' ] );
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getExtra() {
		return $this->hasOne( LoanExtra::className(), [ 'loan_id' => 'id' ] );
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getGuarantor() {
		return $this->hasOne( LoanGuarantor::className(), [ 'loan_id' => 'id' ] );
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getEnterprise() {
		return $this->hasOne( LoanEnterprise::className(), [ 'loan_id' => 'id' ] );
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getChanges() {
		return $this->hasOne( Changes::className(), [ 'type_id' => 'id' ] );
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAppointment() {
		return $this->hasOne( LoanAppointment::className(), [ 'loan_id' => 'id' ] );
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProgress() {
		return $this->hasMany( LoanProgerss::className(), [ 'loan_id' => 'id' ] );
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLoanColor() {
		return $this->hasOne( LoanColor::className(), [ 'id' => 'color_id' ] );
	}

	public function getLoanCSSColor() {
		$cssColorData = $this->hasOne( LoanColor::className(), [ 'id' => 'color_id' ] )->one();
		if ( isset( $cssColorData ) ) {
			$cssColor = $cssColorData['css_color'];
		} else {
			$cssColor = '';
		}

		return $cssColor;
	}

	public function getAllColors() {
		return LoanColor::find();
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBill() {
		return $this->hasOne( Bill::className(), [ 'loan_id' => 'id' ] );
	}

	public function getProgbar() {
		$html = "";

		// naudas
		//if ($this->product == 2 && $this->person->credit_history !== 2) {
		$progresses = $this->getProgressFormated();
		$html       = "<div class='prog-view'>";
		foreach ( $this->getInProgress() as $progressId => $progressTitle ) {
			//inbank iespeja aizdevums credit24

			//if ($progressId == 0 || $progressId == 10 || $progressId == 2 || $progressId == 3 || $progressId == 4) {
			//	continue;
			//}
			if ( $progressId == 0 ) {
				continue;
			}
			$progressVal    = "";
			$progressTxtVal = "";
			$progressClass  = "";
			if ( isset( $progresses[ $progressId ] ) ) {
				$progressData   = $progresses[ $progressId ];
				$progressClass  = $progressData->getBarClass();
				$progressVal    = $progressData->status;
				$progressTxtVal = $progressData->text;
			} else {
				$progressClass = " btn-default";
			}

			$html .= '<div class="prog-view-item ' . $progressClass . '" data-toggle="tooltip" data-placement="top" title="' . $progressTitle . '"></div>';
		}
		$html .= '</div>';
		//}

		// auto lizings
		/*if ($this->product == 1) {
			$progresses = $this->getProgressFormated();
			$html = "<div class='prog-view'>";
			foreach ($this->getInProgress() as $progressId => $progressTitle) {
				//inbank iespeja aizdevums credit24


				//if ($progressId == 0 || $progressId == 2 || $progressId == 4 || $progressId == 6  || $progressId == 8 || $progressId == 10) {
				//	continue;
				//}
				$progressVal = "";
				$progressTxtVal = "";
				$progressClass = "";
				if (isset($progresses[$progressId])) {
					$progressData = $progresses[$progressId];
					$progressClass = $progressData->getBarClass();
					$progressVal = $progressData->status;
					$progressTxtVal = $progressData->text;
				} else {
					$progressClass = " btn-default";
				}

				$html .= '<div class="prog-view-item '. $progressClass.'" data-toggle="tooltip" data-placement="top" title="'.$progressTitle.'"></div>';
			}
			$html.= '</div>';
		}*/

		return $html;
	}

	public function getSources() {
		$sources = [];
		if ( $data = Yii::$app->db->createCommand( "SELECT `source` FROM `loan` group by source;" )->queryAll() ) {
			foreach ( $data as $source ) {
				$sources[ Yii::t( "app/loan", $source["source"] ) ] = Yii::t( "app/loan", $source["source"] );
			}
		}

		return $sources;
	}

	public function getCourier() {
		return [
			"0" => Yii::t( "app/loan", "No" ),
			"1" => Yii::t( "app/loan", "Yes" ),
		];
	}

	/**
	 * inbank    auto izmaksa    0,0413 1
	 * inbank    akcija auto izmaksa    0,0289 2
	 * inabnk    paterina kredits standart    0,0579 3
	 * inabnk    paterina kredits premium    0,0331 5
	 * mogo    auto lizings    0,0413 10
	 * mogo    kredits pret auto kilu    0,0413 11
	 * iespejukredits    naudas kredits    0,0413 16
	 * aizdevums    auto lizings    0,0413 19
	 * aizdevums    naudas kredits    0,0413 17
	 * credit24    naudas kredits    0,0605 20
	 *
	 * @param unknown $model
	 *
	 * @return number
	 */
	public function getCeo( $model ) {
		$co = false;
		if ( $model->status == self::STATUS_CLOSED ) {
			if ( $model->deal_product == 1 ) {
				$co = "0.0413";
			}
			if ( $model->deal_product == 2 ) {
				$co = "0.0289";
			}
			if ( $model->deal_product == 3 ) {
				$co = "0.0579";
			}
			if ( $model->deal_product == 5 ) {
				$co = "0.0331";
			}
			if ( $model->deal_product == 10 ) {
				$co = "0.04132231";
			}
			if ( $model->deal_product == 11 ) {
				$co = "0.04132231";
			}
			if ( $model->deal_product == 12 ) {
				$co = "0.04132231";
			}
			if ( $model->deal_product == 16 ) {
				$co = "0.04132231";
			}
			if ( $model->deal_product == 17 ) {
				$co = "0.04132231";
			}
			if ( $model->deal_product == 18 ) {
				$co = "0.04132231";
			}
			if ( $model->deal_product == 19 ) {
				$co = "0.04132231";
			}
			if ( $model->deal_product == 20 ) {
				$co = "0.05";
			}
			if ( $model->deal_product == 21 ) {
				$co = "0.01239669";
			}
			if ( $model->deal_product == 22 ) {
				$co = "0.02479339";
			}
			if ( $model->deal_product == 23 ) {
				$co = "0.01652893";
			}
			if ( $model->deal_product == 24 ) {
				$co = "0.02479339";
			}
			if ( $model->deal_product == 25 ) {
				$co = "0.03305785";
			}
			if ( $model->deal_product == 26 ) {
				$co = "0.06";
			}
			if ( $model->deal_product == 27 ) {
				$co = "0.05";
			}
			if ( $model->deal_product == 28 ) {
				$co = "0.04132231";
			}
			if ( $model->deal_product == 29 ) {
				$co = "0.06";
			}

			if ( $co ) {
				if ( $model->deal_stage == 3 ) {
					return ( ( $model->amount * $co > 247.933 ) ? 247.933 : $model->amount * $co );
				} else {
					return $model->amount * $co;
				}
			}
		}

		return null;
	}

	public function getValue( $name, $model ) {
		switch ( $name ) {
			case "ceo":
				return $this->getCeo( $model );
				break;
			case "status":
				return $this->getStatuses()[ (int) $model->$name ];
				break;
			case "actions":
				return "Show actions done";
				break;
			case "approved":
				return ( isset( $this->getInProgress()[ (int) $model->$name ] ) ? $this->getInProgress()[ (int) $model->$name ] : null );
				break;
			case "person.credit_history":
				return $model->getCreditHistory()[ (int) $model->credit_history ];
				break;
			case "product":
				return $this->getProducts()[ (int) $model->$name ];
				break;
			case "deal_stage":
				return ( isset( $this->getDealStages()[ (int) $model->$name ] ) ? $this->getDealStages()[ (int) $model->$name ] : null );
				break;
			case "bill_status":
				return ( is_integer( $model->getBillStatus() ) ? ( new Bill() )->getStatuses()[ (int) $model->getBillStatus() ] : null );
				break;
			case "bill_amount":
				return $model->getBillAmount();
				break;
			case "deal_product":
				return ( isset( $model->getDealStageProduct( true )[ (int) $model->deal_product ] ) ? $model->getDealStageProduct( true )[ (int) $model->deal_product ]["title"] : null );
				break;
			case "user_id":
				return $this->getUser( $model->user_id );
				break;
			case "term":
				return $model->term . " m.";
				break;
			case "person.income":
				return $model->income . " €";
				break;
			case "person.outcome":
				return $model->outcome . " €";
				break;
            case "source":

//                $params = \Yii::$app->params['wordpress']['forms'];
                $params = SourceService::getWordpressForms();

                if (isset($params[$model->source])) {
                    return $params[$model->source]['name'];
                } else {
                    return $model->source;
                }

				break;

			case "amount":
				return $model->amount . " €";
				break;
			case "files":
				return ( $model->extra ? ( ( $model->extra->vsaa_statement_ep52 || $model->extra->vsaa_statement || $model->extra->bank_account_statement ) ? "Yes" : "No" ) : "No" );
				break;
			case "prog":
				return $model->getProgbar();
				break;
			case "reminder_time":
				return ( $model->reminder_time ? Yii::$app->formatter->asDate( $model->reminder_time ) : null );
				break;
			case "Zvanits":

				$sC = Changes::find()->where( [
					"attr"    => "actions_61",
					"type"    => 2,
					"type_id" => $model->id
				] )->count();
				$nC = Changes::find()->where( [
					"attr"    => "actions_64",
					"type"    => 2,
					"type_id" => $model->id
				] )->count();

				return $sC + $nC;
				break;
			default:
				return $model->{str_replace( "person.", "", $name )};
		}
	}

	/**
	 * Make actions as array, later in app used
	 * {@inheritDoc}
	 * @see \yii\db\BaseActiveRecord::afterFind()
	 */
	public function afterFind() {
		if ( $this->actions ) {
			$actions = [];
			foreach ( explode( ",", $this->actions ) as $value => $action ) {
				if ( in_array( $action, $this->getActionsFixed() ) ) {
					$actions[ $action ] = 1;
				}
			}
			$this->actions = $actions;
		} else {
			$this->actions = [];
		}

		$this->actionsBefore = $this->actions;


		if ( $this->create_time == $this->update_time ) {
			$time = $this->create_time;
		} else {
			$time = $this->update_time;
		}

		$this->waiting_time = round( abs( time() - $time ) / 60 );

		return parent::afterFind();
	}

	/**
	 * (non-PHPdoc)
	 * @see \yii\base\Model::beforeValidate()
	 */
	public function beforeValidate() {
		if ( ! $this->person_id ) {
			$person = new Person();
			$person->load( $_POST );

			$personCheck = Person::find()->where( [ "personal_code" => trim( $person->personal_code ) ] )->one();
			if ( $personCheck ) {
				$personCheck->load( $_POST );
				$personCheck->save();

				$personId = $personCheck->id;
			} else {
				$person->save();
				$personId = $person->id;
			}

			$this->person_id = $personId;
		}

		$this->rating = $this->setRating();

		if ( ! $this->close_time ) {
			if ( $this->status == self::STATUS_CLOSED || $this->status == self::STATUS_REJECTED ) {
				if ( $this->isAttributeChanged( "status" ) ) {
					$this->close_time = time();
				}
			}
		}

		if ( (int) $this->status !== self::STATUS_CLOSED && (int) $this->status !== self::STATUS_REJECTED ) {
			$this->close_time = null;
		}

		if ( isset( $_POST["Loan"] ) && isset( $_POST["Loan"]["close_time"] ) ) {
			$this->close_time = $_POST["Loan"]["close_time"];
		}

		// reminder time updates
		if ( (int) $this->status == self::STATUS_REJECTED ) {
			$this->reminder_time = null;
		}

		if ( (int) $this->status == self::STATUS_CLOSED ) {
			$this->reminder_time = null;
		}

		if ( (int) $this->status == self::STATUS_REFUSES ) {
			$this->reminder_time = null;
		}

		if ( (int) $this->status !== self::STATUS_NEW && $this->waiting_time ) {
			$this->waiting_time = null;
		}

		if ( (int) $this->status !== self::STATUS_NEW && $this->reminder_class ) {
			$this->reminder_class = 0;
		}

		return parent::beforeValidate();
	}

	public function getActionsFixed() {
		return [
			"66",
			"70",
			"71",
		];
	}

	public function setRatingText() {
		$this->ratingText = $this->setRating( false );
	}

	/**
	 * No sākuma čekojam natural, ja neiziet carui tad mīnus, tad
	 * ja iziet natural čekojam + vai - ja neiziet 0
	 * Tad +
	 * @return number
	 */
	public function setRating( $return = true ) {

		if ( ! in_array( $this->product, [ 1, 2, 5 ] ) ) {
			if ( $return ) {
				return 0;
			} else {
				return "Not expacted loan type";
			}
		}

		if ( $this->person ) {
			// natural start
			if ( $this->amount < 1000 ) {
				if ( $return ) {
					return - 1;
				} else {
					return "Too small loan amount";
				}
			}

			if ( $this->person->getLoans()->where( [
					'between',
					'create_time',
					strtotime( "-12 months" ),
					time()
				] )->count() > 6 ) {
				if ( $return ) {
					return - 1;
				} else {
					return "More then 6 loans in last 12 months";
				}
			}
			if ( $this->person->income < 300 ) {
				if ( $return ) {
					return - 1;
				} else {
					return "Income smaller then 300";
				}
			}
			if ( $this->person->outcome / $this->person->income >= 0.3 ) {
				if ( $return ) {
					return - 1;
				} else {
					return "Outcome/Income ratio smaller then 0.3";
				}
			}

			$birthDay   = substr( $this->person->personal_code, 0, 2 );
			$birthMonth = substr( $this->person->personal_code, 2, 2 );
			$birthYear  = substr( $this->person->personal_code, 4, 2 );
			$birthYear  = ( $birthYear > 20 ? "19" . $birthYear : "20" . $birthYear );
			$age        = date( 'Y' ) - $birthYear;
			try {
				$date1 = new \DateTime( $birthYear . "-" . $birthMonth . "-" . $birthDay );
			} catch ( \Exception $e ) {
				return 0;
			}
			$date2    = new \DateTime( "now" );
			$interval = $date1->diff( $date2 );
			$age      = $interval->y;
			if ( $age < 23 ) {
				if ( $return ) {
					return - 1;
				} else {
					return "Younger then 23. AGE:" . $age;
				}
			}

			$crateDay  = date( "N", $this->create_time );
			$crateHour = date( "H", $this->create_time );
//     		if ($crateDay == 7) {
//     			if (in_array($crateHour, [23,00,01,02])) {
//     				return -1;
//     			}
//     		}
			// natural end


			// minus start
			if ( $this->person->getLoans()->where( [
				'between',
				'create_time',
				strtotime( "-36 months" ),
				time()
			] )->andWhere( [ "product" => 3 ] )->count() ) {
				if ( $return ) {
					return - 1;
				} else {
					return "Has Kredītu pavienošana in last 36 months";
				}
			}
			if ( $this->person->outcome / $this->person->income >= 0.4 ) {
				if ( $return ) {
					return - 1;
				} else {
					return "Outcome/Income ratio smaller then 0.4";
				}
			}
			// minus end


			// plus start
			if ( $this->person->getLoans()->where( [
					'between',
					'create_time',
					strtotime( "-12 months" ),
					time()
				] )->andWhere( [ "product" => 2 ] )->count() > 2 ) {
				return 0;
			}
			if ( $this->person->getLoans()->where( [
					'between',
					'create_time',
					strtotime( "-12 months" ),
					time()
				] )->andWhere( [ "product" => 1 ] )->count() > 3 ) {
				return 0;
			}

			if ( $this->person->outcome / $this->person->income >= 0.2 ) {
				return 0;
			}
			if ( $this->person->income < 340 ) {
				return 0;
			}
			if ( $this->create_time ) {
				if ( in_array( $crateDay, [ 1, 2, 3, 4 ] ) ) {
					if ( in_array( $crateHour, [ 19, 20, 21, 22, 23, 00, 01, 02, 03, 04, 05 ] ) ) {
						return 0;
					}
				}
				if ( in_array( $crateDay, [ 5, 6 ] ) ) {
					if ( in_array( $crateHour, [ 16, 17, 18, 19, 20, 21, 22, 23, 00, 01, 02, 03, 04, 05, 06 ] ) ) {
						return 0;
					}
				}
			}
			// plus end

			if ( $return ) {
				return 1;
			} else {
				return "Pass all + values";
			}
		}

		return 0;
	}

	/**
	 * (non-PHPdoc)
	 * @see \yii\db\BaseActiveRecord::save($runValidation, $attributeNames)
	 */
	public function save( $runValidation = true, $attributeNames = null ) {
		$isNew = $this->isNewRecord;

		$history = [];
		foreach ( $this->getAttributes() as $attrKey => $attrValue ) {
			if ( $attrKey == "actions" ) {
				continue;
			}

			if ( (string) $this->getOldAttribute( $attrKey ) !== (string) $attrValue ) {
				$history[] = [ $attrKey, $this->getOldAttribute( $attrKey ), $attrValue ];
				if ( $attrKey == "reminder_time" && $this->skipReminder == true ) {
					$lastChangedAttr = "reminder_time";
				}
			}
		}

		if ( $isNew ) {
			$history       = [];
			$this->actions = [];
		}

		$actionsToString = "";

		if ( $this->actions ) {
			foreach ( $this->actions as $action => $value ) {
				if ( in_array( $action, $this->getActionsFixed() ) ) { // fixed actions
					if ( in_array( $action, $this->_flip( $this->actionsBefore ) ) ) {
						if ( (int) $value == 0 ) {
							$history[] = [ "actions_" . $action, 1, 0 ];
						} else {
							$actionsToString .= $action . ",";
						}
					} else {
						if ( (int) $value !== 0 ) {
							$history[]       = [ "actions_" . $action, 0, 1 ];
							$actionsToString .= $action . ",";
						}
					}
				} else {
					if ( in_array( $action, $this->actionsBefore ) ) {
						$history[]       = [ "actions_" . $action, 0, 1 ];
						$actionsToString .= $action . ",";
					} else {
						$history[]       = [ "actions_" . $action, 0, 1 ];
						$actionsToString .= $action . ",";
					}
				}
			}
		}

		$this->actions = rtrim( $actionsToString, "," );
		if ( $history ) {
			$this->last_changed_field = ( isset( $lastChangedAttr ) ? $lastChangedAttr : $history[0][0] );
		}

		$needSendAutoRejectEmail = false;
		if ( $this->status == self::STATUS_REFUSES ) {
			if ( $this->getOldAttribute( "status" ) != self::STATUS_REFUSES ) {
				$needSendAutoRejectEmail = true;
			}
		}

		if ( $this->status == self::STATUS_REFUSES || $this->status == self::STATUS_CLOSED || $this->status == self::STATUS_REJECTED ) {
			if ( $this->appointment ) {
				$this->appointment->delete();
			}
		}

		if ( parent::save( $runValidation, $attributeNames ) ) {
			foreach ( $history as $change ) {
				Changes::setChanges( self::TYPE, $this->id, $change[0], $change[1], $change[2] );
			}

			if ( $isNew ) {
				$extra = new LoanExtra();
				$extra->load( $_POST );
				$extra->loan_id = $this->id;
				$extra->save();
			}

			$solr = new Solr();
			$solr->setCollectionUrlByType( self::TYPE );
			$solr->indexByModel( $this );

			if ( $needSendAutoRejectEmail ) {
				$this->afterFind();
				MailController::send( $this, 25 );
			}

			return true;
		}

		return false;
	}

	public function getBillStatus() {
		if ( $this->bill ) {
			return $this->bill->status;
		}

		return null;
	}

	public function getBillAmount() {
		if ( $this->bill ) {
			return $this->bill->amount;
		}

		return null;
	}

	/**
	 * (non-PHPdoc)
	 * @see \yii\db\BaseActiveRecord::afterSave($insert, $changedAttributes)
	 */
	public function afterSave( $insert, $changedAttributes ) {
		parent::afterSave( $insert, $changedAttributes );

		Yii::$app->cache->flush();
		if ( $this->person ) {
			$this->person->loan_count = count( $this->person->loans );
			$this->person->save();
		}
	}

	private function _flip( $array ) {
		$newArray = [];
		$i        = 0;
		foreach ( $array as $key => $value ) {
			$newArray[ $i ] = $key;
			$i ++;
		}

		return $newArray;
	}

    public static function safeUnserialize($serializedData)
    {
        $serializedData = stripslashes($serializedData);

        $serializedData = html_entity_decode($serializedData);

        $serializedData = str_replace(['&#091;', '&#093;'], ['[', ']'], $serializedData);

        // $serializedData = preg_replace('/[^\x20-\x7E]/', '', $serializedData);
        // $serializedData = preg_replace('/[^\x20-\x7E\x{0400}-\x{04FF}]/u', '', $serializedData);
        $serializedData = preg_replace('/[^\x20-\x7E\x{0400}-\x{04FF}\x{0100}-\x{017F}\x{1E00}-\x{1EFF}\x{2C60}-\x{2C7F}]/u', '', $serializedData);

        $fixedSerializedData = preg_replace_callback('/s:(\d+):"(.*?)";/s', function ($matches) {
            $actualLength = strlen($matches[2]);
            return 's:' . $actualLength . ':"' . $matches[2] . '";';
        }, $serializedData);

        $unserializedData = @unserialize($fixedSerializedData);

        if ($unserializedData === false && $fixedSerializedData !== 'b:0;') {
//            error_log("Unable to unserialize data: " . print_r($serializedData, true));
            return [];
        }

        return $unserializedData;
    }

    public static function mapFormToFields( $formData ) {
//	    $formToSource =
//            [1722=>15, 1624 => 16,2917 => 17, 2652 => 18, 1752 => 3, 1448 => 4, 1754 => 5, 1452 => 6, 1757=> 7, 1454 => 8, 1761 => 9, 1456 => 10, 1763 => 11, 1458 => 12, 1765 => 13, 1766 => 14];
        $formToSource = SourceService::getFormToSource();
        $loan     = isset($formData['form_value']) ? self::safeUnserialize($formData['form_value']) : [];
        $property_type = 'J';
        if($loan['sobstvennost']=='Nav nekustamā īpašuma' || $loan['sobstvennost'] == 'Nav nekustam pauma' || $loan['sobstvennost']== '')
        {
        	$property_type = '';
        }
        $tmt_data = $formData['tmt_data'] ?? '';
        $loanPurpose = htmlspecialchars($loan['cel_kredita'] ?? '', ENT_QUOTES, 'UTF-8');
    	$maritalStatus = htmlspecialchars($loan['polojenie'] ?? '', ENT_QUOTES, 'UTF-8');
    	 $form_post_id = $formData['form_post_id'] ?? null;

    $loanType = in_array($form_post_id, [2655, 2656, 2657]) ? 1 : 0;
 	$ret = [
		    "Loan" => [
		        "amount" => (double) ($loan['step_quantity'] ?? ''),
		        "term" => (int) ($loan['step_month'] ?? ''),
		        "source" => $formToSource[$formData['form_post_id'] ?? ''] ?? '',
		        "product" => 1,
		        "description" => $loan['comment'] ?? '',
		        "first_payment" => 0,
		        "referral" => self::prepareReferral($loan),
		        "query_string" => '',
		        "ip_country" => $formData['ip'] ?? '', 
		        "type" => $formData['form_post_id'] ?? '',
		        "tmt_data" => $tmt_data,
		        "loan_purpose" => $loanPurpose,
		        "property_type" => $loan['sobstvennost'] ?? '',
		        "property_address" => $loan['adre_nedv'] ?? '', 
	           	"car_ad_link" => $loan['ssilka'] ?? '',
		        "legal_entity_number" => $loan['reg'] ?? '',
		        "insurance_type" => isset($loan['tip']) && is_array($loan['tip']) && isset($loan['tip'][0]) ? $loan['tip'][0] : '',
		        "car_brand" => $loan['marka_avto'] ?? '',
		        "car_model" => $loan['model_avto'] ?? '',
		        "deposit" => $loan['deposit'] ?? '',
		        "invoice_amount" => $loan['jel_sum_fak'] ?? '',    
				"invoice_due_date" => $loan['jel_srok_fak'] ?? '', 
		        "revenue" => isset($loan['gada']) && is_array($loan['gada']) && isset($loan['gada'][0]) ? $loan['gada'][0] : '',
		     
		        "amount_taken" => (double) ($loan['step_quantity'] ?? ''),
		       'company_length_of_service' => isset($loan['darbojas']) && is_array($loan['darbojas']) && isset($loan['darbojas'][0]) ? (int) $loan['darbojas'][0] : null,
		        'document' => $loan['doc'] ?? '',
		        'realEstate' => $property_type,
		        'agree' => isset($loan['agree']) && $loan['agree'] == 'on' ? 1 : 0,
		    'true_beneficiary' => isset($loan['true_beneficiary']) && $loan['true_beneficiary'] == 'on' ? 1 : 0,
			"step_monthly_payment" => isset($loan['step_monthly_payment']) ? (double) $loan['step_monthly_payment'] : '',
			"total_credit_balance" => isset($loan['sum_ost_vsehkred']) ? (double) $loan['sum_ost_vsehkred'] : '',
			"additional_credit_amount" => isset($loan['dop_sum_kred']) ? (double) $loan['dop_sum_kred'] : '',
		        'politically_significant' => isset($loan['politically_significant']) && $loan['politically_significant'] == 'on' ? 1 : 0,
		        'credit_history' => $loan['credit_history'] ?? 0,
		             'marital_status' => $loan['polojenie'] ?? '',
		             'owned_transport'=>$loan['tranport']??'',
		              'year_of_issue' => $loan['godvipuska'] ?? '',
		             
		              "loan_type" => $loanType,
		    ],
		    "Person" => [
		        "name" => $loan["imya"] ?? '',
		        "surname" => $loan["familiya"] ?? '',
		        "personal_code" => $loan['personalcod'] ?? ($loan['kod'] ?? ''),
		        "phone" => $loan["telefon"] ?? '',
		        "email" => $loan["email"] ?? '',
		        "salary" => (double) (($loan['zarplata'] ?? '') ?: ($loan['netto'] ?? '')),
		        "workplace" => $loan['mesto'] ?? '',
		        "income" => (double) ($loan['zp'] ?? ''),
		        "outcome" => (double) ($loan['plateji'] ?? ''),
		        "house_number" => $loan['ndom'] ?? '',
		        "flat_number" => $loan['nkv'] ?? '', 
		        "city" => $loan['gorod'] ?? '',
		      	"street" => $loan['ulica'] ?? '',
		        "position" => $loan['doljn'] ?? '', 
				'length_of_service' => isset($loan['staj']) ? (int) $loan['staj'] : null,
		        'marital_status' =>  $maritalStatus,
		        "address" => 
		            ($loan['ulica'] ?? '') . 
		            (($loan['ndom'] ?? '') ? ' ' . $loan['ndom'] : '') . 
		            (($loan['nkv'] ?? '') ? ' ' . $loan['nkv'] : '') . 
		            (($loan['gorod'] ?? '') ? ' ' . $loan['gorod'] : '') . 
		            (($loan['adre_nedv'] ?? '') ? ' ' . $loan['adre_nedv'] : ''),
		        "credit_history" => 0,
		         "company_name" => $loan["company_name"] ?? '',
		         "company_duration_months" => $loan["company_work_months"] ?? '',
		         'registration_number'=>$loan['registration_number']??'',
 				'company_turnover'=>$loan['company_turnover']??'',
 				'dependants'=>$loan['ijdiv']??'',
		        // "dependants" => (int) ($loan['ijdiv'] ?? ''),
		        'subscribe' => $loan['predlozenie'] ?? '',

		        'courier' => $loan['kurjer'] ?? ''
		    ],
		    "id" => $formData['form_id'] ?? '',
    		"form_id" => strval(($formData['form_id'] ?? 0) + 6000),
		];

        return $ret;
    }

    /**
     * @param $loan
     * @return false|string
     */
    public static function prepareReferral($loan)
    {
        if (!is_array($loan)) {
            return '';
        }

        $utm = [
            'utm_source',
            'utm_medium',
            'utm_campaign'
        ];

        $referral = array_intersect_key($loan, array_combine($utm, $utm));

        $referral = array_filter($referral);

        return !empty($referral) ? json_encode($referral) : '';
    }

	public static function getSafeParam( $param ) {
		return isset( $param ) ? $param : '';
	}
}

