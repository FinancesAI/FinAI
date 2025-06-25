<?php

namespace app\controllers;

use app\commands\SendController;
use app\components\SolrDataProvider;
use app\models\Changes;
use app\models\Loan;
use app\models\LoanAppointment;
use app\models\LoanGuarantor;
use app\models\LoanProgerss;
use app\models\Person;
use app\models\search\LoanAppointmentSearch;
use app\models\search\LoanSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\base\Controller;
use yii\web\NotFoundHttpException;

/**
 * LoanController implements the CRUD actions for Loan model.
 */
class LoanController extends Controller
{
    /**
     * @inheritdoc
     */
	public function behaviors() {
		return [
			'verbs'  => [
				'class'   => VerbFilter::className(),
				'actions' => [
					'delete' => [ 'POST' ],
				],
			],
			'access' => [
				'class' => AccessControl::className(),
				'only'  => [ 'index', 'create', 'update', 'edit', 'view', 'appointment', 'extra', 'guarantor' ],
				'rules' => [
					[
						'actions'       => [ 'index', 'update', 'create', 'view', 'appointment', 'extra', 'guarantor' ],
						'allow'         => true,
						'matchCallback' => function ( $rule, $action ) {
							return ! Yii::$app->getUser()->getIdentity()->isBank();
						},
					],
				],
			],
		];
	}

    public function actionAppointment()
    {
    	if (\Yii::$app->getRequest()->post("id")) {
    		$delet = LoanAppointment::find()->where(["loan_id" => \Yii::$app->getRequest()->post("id")])->one();
    		if ($delet) {
    			$delet->delete();
    		}
    		echo 1;
    		\Yii::$app->end();
    	}

    	$searchModel = new LoanAppointmentSearch();
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    	$dataProvider->query->orderBy("date asc");

    	return $this->render('appointment', [
    			'searchModel' => $searchModel,
    			'dataProvider' => $dataProvider
    	]);
    }

	/**
	 * Lists all Loan models.
	 * @return mixed
	 * @throws \yii\base\InvalidConfigException
	 */
    public function actionIndex()
    {
    	$model = new Loan();

    	if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
    		if ($model->isNewRecord && !$model->source) {
    			$model->source = "placis";
    		}
    		if ($model->save()) {
    			return $this->redirect(['view', 'id' => $model->id]);
    		}
    	} else {
	        $searchModel = new LoanSearch();
	        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
	        $modelPerson = new Person();
			$modelPerson->load(Yii::$app->request->post());

			if (Yii::$app->setting->get("solr") == 1) {
				if (isset($_COOKIE["global_filter"]) && $_COOKIE["global_filter"] !== "0") {
					$globalFilters = explode(",", ($_COOKIE["global_filter"]));
					$globalQuery = "";
					foreach ($globalFilters as $product) {
						if ($globalQuery) {
							$globalQuery .= " OR ".$product;
						} else {
							$globalQuery = $product;
						}
					}
					$globalQuery = "AND product:($globalQuery)";

					$dataProvider->solr->setQuery($globalQuery, true);
				}
			} else {
				if (isset($_COOKIE["global_filter"]) && $_COOKIE["global_filter"] !== "0") {
					$dataProvider->query->andFilterWhere(["in", "product", explode(",", $_COOKIE["global_filter"])]);
				}
			}

			if (\Yii::$app->urlManager->createUrl(["loan/index", "custom" => "true"]) == \Yii::$app->getRequest()->getUrl() || \Yii::$app->getRequest()->get("custom") && \Yii::$app->getRequest()->get("page")) {

				if (Yii::$app->setting->get("solr") == 1) {
					$newDataProvider = new SolrDataProvider();
					$newDataProvider->solr->setCollectionUrlByType(2);
					$newDataProvider->setClassName("app\models\Loan");
					$newDataProvider->solr->setQuery('status:0 AND user_id:0');
					$newDataProvider->solr->setOrder("sproduct asc, files desc, loan_count asc, create_time desc");

					$newUpdatedDataProvider = new SolrDataProvider();
					$newUpdatedDataProvider->solr->setCollectionUrlByType(2);
					$newUpdatedDataProvider->setClassName("app\models\Loan");
					$newUpdatedDataProvider->solr->setQuery('status:0 AND !last_changed_field:reminder_time AND !user_id:0');
					$newUpdatedDataProvider->solr->setOrder("update_time asc");

					$newUpdatedReminderDataProvider = new SolrDataProvider();
					$newUpdatedReminderDataProvider->solr->setCollectionUrlByType(2);
					$newUpdatedReminderDataProvider->setClassName("app\models\Loan");
					//$newUpdatedReminderDataProvider->solr->setQuery('status:(0 OR 1 OR 2 OR 3) AND last_changed_field:reminder_time AND reminder_time:0');

					if (isset($_COOKIE["global_filter_op"]) && $_COOKIE["global_filter_op"] !== "0") {
					    $newUpdatedReminderDataProvider->solr->setQuery('status:(0 OR 1 OR 2 OR 3) AND last_changed_field:reminder_time AND reminder_time:0');
					} else {
					    $newUpdatedReminderDataProvider->solr->setQuery('status:(0 OR 1 OR 2 OR 3 OR 8) AND last_changed_field:reminder_time AND reminder_time:0');
					}
					$newUpdatedReminderDataProvider->solr->setOrder("update_time asc");

					if (isset($globalQuery)) {
						$newUpdatedDataProvider->solr->setQuery($globalQuery, true);
						$newDataProvider->solr->setQuery($globalQuery, true);
						$newUpdatedReminderDataProvider->solr->setQuery($globalQuery, true);
					}

				} else {
					$newDataProvider = new ActiveDataProvider([
							'query' => Loan::find()->joinWith(["changes", "person"])->where(["status" => Loan::STATUS_NEW])->andWhere(["is", "changes.type_id", null])->orderBy([new Expression('update_time desc'), new Expression('FIELD (person.credit_history, 1) desc')]),
							'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
							'pagination' => [
									'pagesize' => Yii::$app->setting->get("page_limit"),
							],
					]);
					$newUpdatedDataProvider = new ActiveDataProvider([
							'query' => Loan::find()->joinWith(["changes", "person"])->where(["status" => Loan::STATUS_NEW])->andWhere(["is not", "changes.id", null])->groupBy("changes.type_id")->orderBy([new Expression('update_time desc'), new Expression('FIELD (person.credit_history, 1) desc')]),
							'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
							'pagination' => [
									'pagesize' => Yii::$app->setting->get("page_limit"),
							],
					]);

					if (isset($_COOKIE["global_filter"]) && $_COOKIE["global_filter"] !== "0") {
						$newUpdatedDataProvider->query->andFilterWhere(["in", "product", explode(",", $_COOKIE["global_filter"])]);
						$newDataProvider->query->andFilterWhere(["in", "product", explode(",", $_COOKIE["global_filter"])]);
					}

					$newUpdatedReminderDataProvider = null;
				}

				return $this->render('custom_index', [
						'searchModel' => $searchModel,
						'dataProvider' => $newDataProvider,
						'dataProvider2' => $newUpdatedDataProvider,
						'dataProvider3' => $newUpdatedReminderDataProvider,
						'model' => $model,
						'modelPerson' => $modelPerson,
				]);
			}

	        return $this->render('index', [
	            'searchModel' => $searchModel,
	            'dataProvider' => $dataProvider,
	            'model' => $model,
	        	'modelPerson' => $modelPerson,
	        ]);
    	}
    }

    /**
     * Displays a single Loan model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
    	$model = $this->findModel($id);

    	if (\Yii::$app->getRequest()->post("task") == "remove-app") {
    		$delet = LoanAppointment::find()->where(["loan_id" => \Yii::$app->getRequest()->post("id")])->one();
    		if ($delet) {
    			$delet->delete();
    		}
//    		echo 1;
//    		\Yii::$app->end();
            return 1;
    	}

    	if (\Yii::$app->getRequest()->post("task") == "save-progress") {
    		$progressData = LoanProgerss::find()->where(["loan_id" => $id, "provider_id" => \Yii::$app->getRequest()->post("provider")])->one();
    		if (!$progressData) {
    			$progressData = new LoanProgerss();
    			$progressData->loan_id = $id;
    			$progressData->provider_id = \Yii::$app->getRequest()->post("provider");
    		}
    		$progressData->text = \Yii::$app->getRequest()->post("text");
    		$progressData->status = \Yii::$app->getRequest()->post("status");
    		$progressData->amount = \Yii::$app->getRequest()->post("amount");

    		return json_encode([
    				"save" => $progressData->save(),
    				"class" => $progressData->getBarClass()
    		]);
    	}

    	if (\Yii::$app->getRequest()->get("task") == "send-ext-email") {

    		if ($model->source == "placis") {
    			SendController::sendExternalEmail($model->unique_id, "placis");
    			Changes::setChanges(Loan::TYPE, $model->id, "actions_21", null, 21);
    			\Yii::$app->getSession()->setFlash("success", \Yii::t("app/mail", "Messege sent."));
    		}

    		if ($model->source == "1lizings") {
    			SendController::sendExternalEmail($model->unique_id, "1lizings");
    			Changes::setChanges(Loan::TYPE, $model->id, "actions_21", null, 21);
    			\Yii::$app->getSession()->setFlash("success", \Yii::t("app/mail", "Messege sent."));
    		}

    		return $this->redirect(["loan/view", "id" => $id]);
    	}

    	if (Yii::$app->request->post("reminder_time")) {
    		$model->reminder_time = Yii::$app->request->post("reminder_time");
    		$model->reminder_class = Yii::$app->request->post("reminder_class");
    		$model->save();
    		return Yii::$app->formatter->asDate($model->reminder_time);
    	}

    	if (Yii::$app->request->post("app_time")) {
    		$appModel = LoanAppointment::find()->where(["loan_id" => $model->id])->one();

    		if (!$appModel) {
    			$appModel = new LoanAppointment();
    		}
    		$appModel->loan_id = $model->id;
    		$appModel->date = Yii::$app->request->post("app_time");
    		$appModel->save();

    		return Yii::$app->formatter->asDate($appModel->date);
    	}

    	if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
    		// Kredītu pasaule
    		if ((int)$model->getOldAttribute("status") !== (int)$model->getAttribute("status")) {
    			if ((int)$model->status == 4 || (int)$model->status == 7) {
    				//$cmd = PHP_BINDIR . '/php ' . \Yii::getAlias('@app') . '/yii send/kreditu-pasaule "'.$model->person->email.'" > /dev/null &';
    				//exec($cmd);
    			}
    		}
    		//kreditu pasaule

    		if ($model->save()) {
    			if (\Yii::$app->getRequest()->isAjax) {
    				return 1;
    			}
    			return $this->refresh();
    		}
    	} else {
    		$needSave = false;
    		//if (!$model->reminder_time && $model->reminder_class) {
    			//$model->reminder_class = 0;
    			//$needSave = true;
    		//}
    		if ($model->status == 0 && $model->user_id == null && Yii::$app->getUser()->getIdentity()->isAdmin() == false) {
    			$model->status = Loan::STATUS_IN_PROGRES;
    			$model->user_id = Yii::$app->getUser()->getIdentity()->id;
    			$needSave = true;
    		} else {
    			if ($model->status == 0 && Yii::$app->getUser()->getIdentity()->isAdmin() == false) {
    				$model->status = Loan::STATUS_IN_PROGRES;
    				$needSave = true;
    			}
    		}

    		if ($needSave) {
    			$model->save();
    		}

    		return $this->render('view', [
    				'model' => $model,
    		]);
    	}
    }


    /**
     * Displays a single Loan model.
     * @param string $id
     * @return mixed
     */
    public function actionExtra($id)
    {
    	$model = $this->findModel($id);

    	if (Yii::$app->request->post() && $model->extra->load(Yii::$app->request->post()) && $model->extra->save()) {
    		if (\Yii::$app->getRequest()->isAjax) {
    			return 1;
    		}
    	}

    	return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Displays a single Loan model.
     * @param string $id
     * @return mixed
     */
    public function actionGuarantor($id)
    {
    	$model = $this->findModel($id);

    	if ($model->guarantor) {
    		$gur = $model->guarantor;
    	} else {
    		$gur = new LoanGuarantor();
    		$gur->loan_id = $id;
    	}
    	$gur->load(Yii::$app->request->post());
    	$gur->save();

    	if (\Yii::$app->getRequest()->isAjax) {
    		return 1;
    	}

    	return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Finds the Loan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Loan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Loan::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
