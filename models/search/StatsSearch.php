<?php

namespace app\models\search;

use app\models\LoanProgerss;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Loan;
use app\models\Person;
use app\models\User;
use app\models\Changes;

/**
 * LoanSearch represents the model behind the search form about `app\models\Loan`.
 */
class StatsSearch extends Loan
{
	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $period = null)
    {
    	$periodSql = "";
    	if ($period == "month") {
    		$periodSql = "close_time >= ".strtotime('first day of '.date( 'F Y', time()));
    	}
    	
    	if ($period == "week") {
    		$periodSql = "close_time >= ".strtotime('last Monday', time());
    	}
    	
    	$query = Loan::find();
    	 
    	if ($periodSql) {
    		$query = $query->where($periodSql);
    	}
		
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (Yii::$app->getRequest()->get("user_id")) {
            $query->joinWith([
                'progress' => function($query) {
                    $query->from(['progerss' => 'loan_progerss'])->andWhere(['progerss.provider_id' => Yii::$app->getRequest()->get("user_id")]);
                }
            ]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
//            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'loan.source' => Yii::$app->getRequest()->get("source"),
            'loan.product' => Yii::$app->getRequest()->get("product"),
            'loan.deal_stage' => Yii::$app->getRequest()->get("deal_stage"),
        	'loan.status' => Loan::STATUS_CLOSED,
        ]);
        
        if (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to"))
        	$query->andFilterWhere(['between', 'close_time', $this->getFrom(), $this->getTo()]);
        
        return $query->sum("loan.amount");
    }

    /**
     * @param $period
     * @return bool|int|mixed|string|null
     */
    public function getSumClosedLoan($period = null)
    {
        $periodSql = "";

        if ($period == "month") {
            $periodSql = "close_time >= ".strtotime('first day of '.date( 'F Y', time()));
        }

        if ($period == "week") {
            $periodSql = "close_time >= ".strtotime('last Monday', time());
        }

        $query = LoanProgerss::find();

        if ($periodSql) {
            $query = $query->where($periodSql);
        }

        $query->joinWith('loan');


        // grid filtering conditions
        $query->andFilterWhere([
            'provider_id' => Yii::$app->getRequest()->get("user_id"),
            'loan.source' => Yii::$app->getRequest()->get("source"),
            'loan.product' => Yii::$app->getRequest()->get("product"),
            'loan.deal_stage' => Yii::$app->getRequest()->get("deal_stage"),
            'loan_progerss.status' => 4,
        ]);

        if (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to"))
            $query->andFilterWhere(['between', 'close_time', $this->getFrom(), $this->getTo()]);

        return $query->sum("loan.amount");
    }
	
    public function getFrom() {
    	return strtotime("midnight", strtotime(Yii::$app->getRequest()->get("from")));
    }
	
    public function getTo() {
    	return strtotime("tomorrow", strtotime(Yii::$app->getRequest()->get("to"))) - 1;
    }
	
    public function getTooltip() {
    	if (Yii::$app->getRequest()->get("to")) {
    		return date("d.m.Y H:m:s", $this->getFrom())." - ".date("d.m.Y H:m:s", $this->getTo());
    	}
    	
    	return "";
    }
    
	private function _getFilterParams($query) {

		if ($query->modelClass == "app\models\Loan") {

            if (Yii::$app->getRequest()->get("user_id")) {
                $query->joinWith([
                    'progress' => function($query) {
                        $query->from(['progerss' => 'loan_progerss'])->andWhere(['progerss.provider_id' => Yii::$app->getRequest()->get("user_id")]);
                    }
                ]);
            }

//			$query->andFilterWhere([
//	            'user_id' => Yii::$app->getRequest()->get("user_id"),
//	        ]);

            $query->andFilterWhere([
                'loan.source' => Yii::$app->getRequest()->get("source"),
                'loan.product' => Yii::$app->getRequest()->get("product"),
                'loan.deal_stage' => Yii::$app->getRequest()->get("deal_stage"),
            ]);

            if (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")) {
                $query->andFilterWhere(['between', 'create_time', $this->getFrom(), $this->getTo()]);
            }
		}

        if ($query->modelClass == "app\models\Person")
        	if (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to"))
        		$query->andFilterWhere(['between', 'create_time', $this->getFrom(), $this->getTo()]);
        	 
        return $query;
	}
	
    public function getSources() {
    	$sources = [];
    	if ($data = Yii::$app->db->createCommand("SELECT `source` FROM `loan` group by source;")->queryAll())
    		foreach ($data as $source)
    			$sources[$source["source"]] = $source["source"];

    	return $sources;
    }
    
    public function statusBarData() {
    	$loan = new Loan();
    	
		$filterDate = $this->_getFilterParams(Loan::find());
		 
		$data = [];
		if ($filterDate) {
			foreach ($loan->getStatuses() as $statusId => $statusName) {
				$data[$statusName] = $this->_getFilterParams(Loan::find()->where(["loan.status" => $statusId]))->count();
			}
			$data["All"] = $filterDate->count();
		} else {
			foreach ($loan->getStatuses() as $statusId => $statusName) {
				$data[$statusName] = $this->_getFilterParams(Loan::find()->where(["loan.status" => $statusId]))->count();
			}
			$data["All"] = Loan::find()->count();
		}
		
		return $data;
    }
    
    public function clientsCount() {
    	$clientsCount = $this->_getFilterParams(Loan::find()->select('loan.person_id')->distinct());

    	return $clientsCount->count('person_id');
    }
    
    public function compareMore($offset) {
    	$period = range($offset, $offset+2);
    	$html = "";
    	foreach ($period as $month) {
    		$prevMonthStart = strtotime('first day of '.date( 'F Y', strtotime("-".$month." months")));
    		$prevMonthEnd = strtotime('last day of '.date( 'F Y', strtotime("-".$month." months")));
    		$prevMonthEnd = strtotime("tomorrow", $prevMonthEnd) - 1;
    		
    		
    		$prevMonth = Loan::find()->andFilterWhere([
    				'between',
    				'create_time',
    				$prevMonthStart,
    				$prevMonthEnd
    		])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->count();
    		 
    		
    		$prevMonthUq = Loan::find()->andFilterWhere([
    				'between',
    				'create_time',
    				$prevMonthStart,
    				$prevMonthEnd
    		])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->groupBy("person_id")->count();
    		
    		$prevMonthUqF = Loan::find()->joinWith("extra")->where(["loan_extra.bank_account_statement" => ""])->andFilterWhere([
    				'between',
    				'create_time',
    				$prevMonthStart,
    				$prevMonthEnd
    		])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->groupBy("person_id")->count();
    		 
    		$prevMonthClosed = Loan::find()->andFilterWhere([
    				'between',
    				'create_time',
    				$prevMonthStart,
    				$prevMonthEnd,
    		])->andFilterWhere(["status" => Loan::STATUS_CLOSED])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->count();
    		 
    		$prevMonthSum = Loan::find()->andFilterWhere([
    				'between',
    				'create_time',
    				$prevMonthStart,
    				$prevMonthEnd,
    		])->andFilterWhere(["status" => Loan::STATUS_CLOSED])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->sum("amount");
    		
    		//0 => ["sum" => $prev2MonthSum, "tooltip" => $prev2MonthTooltip, "month" => , "count" => $prev2MonthClosed,  "countUnique" => $prev2MonthUq, "countUniqueF" => $prev2MonthUqF],
    		
    		if (!$prevMonthUq) {
    			continue;
    		}
    		
    		$html .= '<div class="col-xs-4">
					<div class="well">
					 	<h2 class="text-center" data-toggle="tooltip" data-placement="top" title="">'.date("F", $prevMonthStart).'</h2>
						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Total loan count"><small>'.$prevMonth.'</small></p>
						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Total unique loan count"><small>'.$prevMonthUq.'</small></p>
						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Fully completed loans"><small>'.($prevMonthUq-$prevMonthUqF).' - '.number_format((($prevMonthUq-$prevMonthUqF)/$prevMonthUq)*100, 2).'%</small></p>
						<p class="text-center mb5"  data-toggle="tooltip" data-placement="top" title="Paid out loan count">'.$prevMonthClosed.'</p>
						<p class="text-center mb5"  data-toggle="tooltip" data-placement="top" title="CR - paid out/unique count">'.number_format($prevMonthClosed/$prevMonthUq, 3).'</p>
						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Paid out amount">'.$prevMonthSum.' €</p>';
			
    		foreach (User::find()->where(["role" => 0])->all() as $user) {
    			if (!Yii::$app->setting->get("user_stats")) {
    				continue;
    			}
    			if ($user->id == 8) {
    				continue;
    			}
    			$u = Changes::find()->where(["user_id" => $user->id])->andWhere(["type" => "2"])->andFilterWhere([
    					'between',
    					'create_time',
    					$prevMonthStart,
    					$prevMonthEnd,
    			])->count();
    			$u2a = Changes::find()->where(["user_id" => $user->id])->andWhere(["type" => "2"])->andFilterWhere([
    					'between',
    					'create_time',
    					$prevMonthStart,
    					$prevMonthEnd,
    			])->groupBy("type_id")->count();
    			$u2 = @($u/$u2a);
    			$html .= '<p class="text-center mb5">

'.$user->fullname.' 
 - 
<span data-toggle="tooltip" data-placement="top" title="'.$user->fullname.' - '.$u.' changes made">'.$u.'</span> 
 - 
<span data-toggle="tooltip" data-placement="top" title="'.$user->fullname.' - '.number_format($u2, 2).' avarage per unit">'.number_format($u2, 2).'</span> </p>';
			}
    		
			$html .= '</div>
				</div>';
    	}
    	
    	return $html;
    }
    
    public function compare() {
    	$thisMonthStart = strtotime('first day of '.date( 'F Y', time()));
    	$thisMonthEnd = strtotime('last day of '.date( 'F Y', time()));
    	$thisMonthEnd = strtotime("tomorrow", $thisMonthEnd) - 1;

    	$thisMonth = Loan::find()->andFilterWhere([
    			'between',
    			'create_time',
    			$thisMonthStart,
    			$thisMonthEnd
    	])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->count();

    	$thisMonthUq = Loan::find()->andFilterWhere([
    			'between',
    			'create_time',
    			$thisMonthStart,
    			$thisMonthEnd
    	])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->groupBy("person_id")->count();
    	
    	$thisMonthUqF = Loan::find()->joinWith("extra")->where(["loan_extra.bank_account_statement" => ""])->andFilterWhere([
    			'between',
    			'create_time',
    			$thisMonthStart,
    			$thisMonthEnd
    			])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->groupBy("person_id")->count();
    	 
    	$thisMonthClosed = Loan::find()->andFilterWhere([
    			'between',
    			'close_time',
    			$thisMonthStart,
    			$thisMonthEnd,
    	])->andFilterWhere(["status" => Loan::STATUS_CLOSED])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->count();
    	
    	$thisMonthSum = Loan::find()->andFilterWhere([
    			'between',
    			'close_time',
    			$thisMonthStart,
    			$thisMonthEnd,
    	])->andFilterWhere(["status" => Loan::STATUS_CLOSED])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->sum("amount");
    	
    	$prevMonthStart = strtotime('first day of '.date( 'F Y', strtotime("-1 months")));
    	$prevMonthEnd = strtotime('last day of '.date( 'F Y', strtotime("-1 months")));
    	$prevMonthEnd = strtotime("tomorrow", $prevMonthEnd) - 1;

    	$prevMonth = Loan::find()->andFilterWhere([
    			'between',
    			'create_time',
    			$prevMonthStart,
    			$prevMonthEnd
    	])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->count();
    	

    	$prevMonthUq = Loan::find()->andFilterWhere([
    			'between',
    			'create_time',
    			$prevMonthStart,
    			$prevMonthEnd
    	])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->groupBy("person_id")->count();
    	$prevMonthUqF = Loan::find()->joinWith("extra")->where(["loan_extra.bank_account_statement" => ""])->andFilterWhere([
    			'between',
    			'create_time',
    			$prevMonthStart,
    			$prevMonthEnd
    			])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->groupBy("person_id")->count();
    	
    	$prevMonthClosed = Loan::find()->andFilterWhere([
    			'between',
    			'close_time',
    			$prevMonthStart,
    			$prevMonthEnd,
    	])->andFilterWhere(["status" => Loan::STATUS_CLOSED])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->count();
    	
    	$prevMonthSum = Loan::find()->andFilterWhere([
    			'between',
    			'close_time',
    			$prevMonthStart,
    			$prevMonthEnd,
    	])->andFilterWhere(["status" => Loan::STATUS_CLOSED])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->sum("amount");


    	$prev2MonthStart = strtotime('first day of '.date( 'F Y', strtotime("-2 months")));
    	$prev2MonthEnd = strtotime('last day of '.date( 'F Y', strtotime("-2 months")));
    	$prev2MonthEnd = strtotime("tomorrow", $prev2MonthEnd) - 1;

    	$prev2Month = Loan::find()->andFilterWhere([
    			'between',
    			'create_time',
    			$prev2MonthStart,
    			$prev2MonthEnd
    	])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->count();  
    	
    	$prev2MonthUq = Loan::find()->andFilterWhere([
    			'between',
    			'create_time',
    			$prev2MonthStart,
    			$prev2MonthEnd
    	])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->groupBy("person_id")->count();
    	$prev2MonthUqF = Loan::find()->joinWith("extra")->where(["loan_extra.bank_account_statement" => ""])->andFilterWhere([
    			'between',
    			'create_time',
    			$prev2MonthStart,
    			$prev2MonthEnd
    			])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->groupBy("person_id")->groupBy("person_id")->count();

    	$prev2MonthClosed = Loan::find()->andFilterWhere([
    			'between',
    			'close_time',
    			$prev2MonthStart,
    			$prev2MonthEnd,
    	])->andFilterWhere(["status" => Loan::STATUS_CLOSED])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->count();

    	$prev2MonthSum = Loan::find()->andFilterWhere([
    			'between',
    			'close_time',
    			$prev2MonthStart,
    			$prev2MonthEnd,
    	])->andFilterWhere(["status" => Loan::STATUS_CLOSED])->andFilterWhere([
            'user_id' => Yii::$app->getRequest()->get("user_id"),
        	'source' => Yii::$app->getRequest()->get("source"),
            'product' => Yii::$app->getRequest()->get("product"),
        ])->sum("amount");
    	
    	$prev2MonthTooltip = "".date("d.m.Y H:m:s", $prev2MonthStart)." - ".date("d.m.Y H:m:s", $prev2MonthEnd);
    	$prevMonthTooltip = "".date("d.m.Y H:m:s", $prevMonthStart)." - ".date("d.m.Y H:m:s", $prevMonthEnd);
    	$thisMonthTooltip = "".date("d.m.Y H:m:s", $thisMonthStart)." - ".date("d.m.Y H:m:s", $thisMonthEnd);
    	
    	return [
    			0 => ["s" => $prev2MonthStart,"e" => $prev2MonthEnd,"sum" => $prev2MonthSum, "tooltip" => $prev2MonthTooltip, "month" => date("F", $prev2MonthStart), "count" => $prev2MonthClosed, "countAll" => $prev2Month, "countUnique" => $prev2MonthUq, "countUniqueF" => $prev2MonthUqF],
    			1 => ["s" => $prevMonthStart,"e" => $prevMonthEnd,"sum" => $prevMonthSum, "tooltip" => $prevMonthTooltip, "month" => date("F", $prevMonthStart), "count" => $prevMonthClosed, "countAll" => $prevMonth, "countUnique" => $prevMonthUq, "countUniqueF" => $prevMonthUqF],
    			2 => ["s" => $thisMonthStart,"e" => $thisMonthEnd,"sum" => $thisMonthSum, "tooltip" => $thisMonthTooltip, "month" => date("F", $thisMonthStart), "count" => $thisMonthClosed, "countAll" => $thisMonth, "countUnique" => $thisMonthUq, "countUniqueF" => $thisMonthUqF],
    	];
    }
    
    public function loansCount() {
    	$loansCount = $this->_getFilterParams(Loan::find());

		return $loansCount->count();
    }
    
    public function loansClosedCount() {
    	$loansClosed = $this->_getFilterParams(Loan::find()->where(["loan.status" => Loan::STATUS_CLOSED]));
		
		return $loansClosed->count();
    }
}
