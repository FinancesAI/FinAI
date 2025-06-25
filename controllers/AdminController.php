<?php

namespace app\controllers;

use Yii;
use app\base\Controller;

use yii\filters\AccessControl;
use app\models\Property;
use app\models\search\LoanSearch;
use app\models\Loan;


class AdminController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'modules'],
                'rules' => [
					[
						'actions' => ['index', 'modules'],
						'allow' => true,
						'matchCallback' => function($rule, $action){return Yii::$app->getUser()->getIdentity()->isAdmin(); },
					],
                ],
            ],
        ];
    }
    
    public function actionIndex()
    {
    	if (Yii::$app->getRequest()->get("task") == "clear-cache") {
    		Yii::$app->cache->flush();
    		return $this->redirect(["index"]);
    	}
    	
    	if (Yii::$app->getRequest()->get("task") == "clear-export") {
    		@unlink(Yii::$aliases["@app"]."/export_post");
    		@unlink(Yii::$aliases["@app"]."/export_object");
    		return $this->redirect(["index"]);
    	}
    	
		return $this->render("index");
    }
    
    public function actionModules()
    {
    	return $this->render("modules");
    }
    
    public function actionStats()
    {
    	return $this->render("stats");
    }
    
    public function actionFinance()
    {
    	if (Yii::$app->getRequest()->post("export")) {
    		$dataPost = [];
    		$dataPost["LoanSearch"]["status"] = 5;
    		$search = new LoanSearch();
    		$dataProvider = $search->search($dataPost);
    		
    		$betweenPrefix = "loan";
    		$between = null;
    		if (\Yii::$app->getRequest()->post("from")) {
    			$between = ['>', $betweenPrefix.".".\Yii::$app->getRequest()->post("what"), $this->getFrom()];
    		}
    		
    		if (\Yii::$app->getRequest()->post("to")) {
    			$between = ['>', $betweenPrefix.".".\Yii::$app->getRequest()->post("what"), $this->getTo()];
    		}
    		
    		if (\Yii::$app->getRequest()->post("from") && \Yii::$app->getRequest()->post("to")) {
    			$between = ['between', $betweenPrefix.".".\Yii::$app->getRequest()->post("what"), $this->getFrom(), $this->getTo()];
    		}
    		
    		if ($between) {
    			if (property_exists($dataProvider, "query")) {
    				$exportData = $dataProvider->query->select("*")->andFilterWhere($between);
    			} else {
    				$exportData = $dataProvider->solr->select("*");
    				$exportData = $dataProvider->solr->setFilterQuery("close_time:[".$this->getFrom()." TO ".$this->getTo()."]");
    			}
    		} else {
    			if (property_exists($dataProvider, "query")) {
    				$exportData = $dataProvider->query->select("*");
    			} else {
    				$exportData = $dataProvider->solr->select("*");
    			}
    		}
    		
    		
    		
    		$post = [];
    		$post["id"] = 1;
    		$post["close_time"] = 1;
    		$post["deal_stage"] = 1;
    		$post["product"] = 1;
    		$post["amount"] = 1;
    		$post["ceo"] = 1;
    		$post["person.name"] = 1;
    		$post["person.surname"] = 1;
    		$post["person.personal_code"] = 1;
    		$post["bill_amount"] = 1;
    		//$post["referral"] = 1;
    		//$post["query_string"] = 1;
    		
    		$json = serialize($post);
    		
    		if (!file_exists(\Yii::getAlias('@app')."/export_object")) {
    			file_put_contents(\Yii::getAlias('@app')."/export_object", serialize($dataProvider));
    			file_put_contents(\Yii::getAlias('@app')."/export_post", serialize($post));
    			$cmd = PHP_BINDIR . '/php ' . \Yii::getAlias('@app') . '/yii loan/export 1 0 "xlsx" '.(int)\Yii::$app->getUser()->getId().' > /dev/null &';
    			exec($cmd);
    				
    			\Yii::$app->getSession()->setFlash("success", "Export process started. You will recive email when export will finish.");
    		} else {
    			\Yii::$app->getSession()->setFlash("danger", "Some other export in progress. Try later.");
    		}
    		return $this->redirect(["finance"]);
    	}
    	
    	$total = null;
    	if (Yii::$app->getRequest()->post("total")) {
    		$between = null;
    		if (\Yii::$app->getRequest()->post("from")) {
    			$between = ['>', "close_time", $this->getFrom()];
    		}
    		if (\Yii::$app->getRequest()->post("to")) {
    			$between = ['>', "close_time", $this->getTo()];
    		}
    		if (\Yii::$app->getRequest()->post("from") && \Yii::$app->getRequest()->post("to")) {
    			$between = ['between', "close_time", $this->getFrom(), $this->getTo()];
    		}
    		
    		if ($between) {
    			$loans = Loan::find()->where(["status" => 5])->andFilterWhere($between)->all();
    		} else {
    			$loans = Loan::find()->where(["status" => 5])->all();
    		}
    		
    		$total = 0;
    		$totalCeo = 0;
    		$totalAm = 0;
    		foreach ($loans as $loan) {
    			$bill = $loan->getValue("bill_amount", $loan);
    			if ($bill) {
    				$bill = $bill/1.21;
    			}
    			$amo = $loan->getCeo($loan)+$bill;
    			$total = $total+$amo;
    			$totalCeo = $totalCeo+$loan->getCeo($loan);
    			$totalAm = $totalAm+$bill;
    		}
    		
    		return $this->render("finance", ["total" => $total, "totalCeo" => $totalCeo, "totalAm" => $totalAm]);
    	}
    	
		return $this->render("finance");
    }
    
    public function getFrom() {
    	return strtotime("midnight", strtotime(\Yii::$app->getRequest()->post("from")));
    }
    
    public function getTo() {
    	return strtotime("tomorrow", strtotime(\Yii::$app->getRequest()->post("to"))) - 1;
    }
}
