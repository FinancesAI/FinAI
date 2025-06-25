<?php

namespace app\controllers;

use Yii;
use app\models\Mail;
use app\models\search\MailSearch;
use yii\filters\AccessControl;
use app\base\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Loan;
use app\models\Gmail;
use yii\web\UploadedFile;
use app\models\Changes;
use app\models\LoanApi;

/**
 * MailController implements the CRUD actions for Mail model.
 */
class MailController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
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

    /**
     * Lists all Mail models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Mail model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Mail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Mail();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Mail model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Mail model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    /**
     * Send mail to loan person.
     * @param string $id
     * @param string $type
     * @return mixed
     */
    public function actionSend()
    {
    	if (Yii::$app->getRequest()->isPost) {

    		$id = \Yii::$app->getRequest()->post("id");
    		$type = \Yii::$app->getRequest()->post("type");

    		Yii::error('-------------------ID ' . $id);

    		$model = $this->findLoanModel($id);

    		
    		if ($this->_send($model, $type)) {
    			if (\Yii::$app->getRequest()->isAjax) {
    				return 1;
    			}
    			\Yii::$app->getSession()->setFlash("success", \Yii::t("app/mail", "Messege sent."));
    		} else {
    			if (\Yii::$app->getRequest()->isAjax) {
    				return 0;
    			}
    			\Yii::$app->getSession()->setFlash("danger", \Yii::t("app/mail", "Messege not sent, try again."));
    		}
    	}
    
    	return $this->redirect(["loan/view", "id" => $id]);
    }
    
    public static function send($model, $type) {
    	return self::_send($model, $type);
    }
    
    
    private function _send($model, $type) {
    	$mail = Mail::find()->where(["custom_id" => $type])->one();
    	
    	$id = $model->id;
    	
    	if (!$model->source) {
    		if (IS_WEB) {
    			\Yii::$app->getSession()->setFlash("danger", \Yii::t("app/mail", "Messege not sent, no loan source detected."));
    			return $this->redirect(["loan/view", "id" => $id]);
    		} else {
    			return false;
    		}
    	}
    	
    	//configurate mailer
    	Yii::$app->mailer->setTransport(\Yii::$app->params["mailer"]["system"]);
    	
    	// set api params
    	if ($mail->api_type) {
    		$newAccess = new LoanApi();
    		$newAccess->loan_id = $id;
    		$newAccess->type = $mail->api_type;
    		$newAccess->url_hash = $newAccess->generateHash();
    		$newAccess->password = $newAccess->generatePassword($model->person->personal_code);
    	
    		if (!$newAccess->save()) {
    			\Yii::$app->getSession()->setFlash("danger", \Yii::t("app/mail", "Error generating link data."));
    			return $this->redirect(["loan/view", "id" => $id]);
    		}
    	
    		// set link for replacement
    		$mail->setLink($newAccess->url_hash);
    	}
    	
    	$html = $mail->getHtml($model->source, $model);
    	
    	$mailer = Yii::$app->mailer->compose()
    	->setFrom('info@carsoutlet.lv')
    	->setReplyTo('info@carsoutlet.lv')
    	->setTo($model->person->email)
    	->setSubject($mail->getSubject($model->source))
    	->setHtmlBody($html)
    	->setTextBody(trim(strip_tags($html)));
    	
    	// file upload check
    	$files = UploadedFile::getInstancesByName("email_custom_file");
    	if ($type == 9 && $files || $type == 11 && $files || $type == 26 && $files) {
    		foreach ($files as $file) {
    			$file->saveAs('uploads/' . $file->baseName . '.' . $file->extension);
    			$mailer->attach('uploads/' . $file->baseName . '.' . $file->extension);
    		}
    	}

	    if ($type == 6) {
    		$mailer->attach('uploads/mail_template_application.docx', ['fileName' => 'Veidlapa.doc']);
	    }
    	
    	// send email
    	if ($mailer->send()) {
    		if ($type == 9 && $files || $type == 11 && $files || $type == 26 && $files) {
    			foreach ($files as $file) {
    				@unlink('uploads/' . $file->baseName . '.' . $file->extension);
    			}
    		}
    		
    		if (in_array(sprintf("%02d", $type), $model->actions)) {
    			Changes::setChanges(Loan::TYPE, $model->id, "actions_".sprintf("%02d", $type), null, sprintf("%02d", $type));
    		} else  {
    			$model->setAttribute("actions", $model->actions + [sprintf("%02d", $type) => 1]);
    			$model->save();
    		}
    		 
			return true;
    	} else {
			return false;
    	}
    }
    
    /**
     * Gmail message lists
     * @param unknown $id
     */
    public function actionGmail($id) {
    	$model = $this->findLoanModel($id);
    
    	if (Yii::$app->getRequest()->isPost) {
    		$gmail = new Gmail();
    
    		$gmail->setModel($model);
    
    		echo $gmail->getMessages();
    	}
    
    	\Yii::$app->end();
    }
    
    /**
     * Finds the Mail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Mail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Mail::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * Finds the Loan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Loan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findLoanModel($id)
    {
    	if (($model = Loan::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
}
