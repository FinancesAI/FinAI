<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\search\UserSearch;
use app\base\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\form\LoginForm;
use app\models\UserCalendar;
use yii\filters\AccessControl;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'logout', 'index', 'create', 'update', 'edit', 'view', 'delete'],
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
					[
						'actions' => ['index', 'update', 'create', 'view', 'delete'],
						'allow' => true,
						'matchCallback' => function($rule, $action) { return Yii::$app->getUser()->getIdentity()->isAdmin(); },
					],
                ],
            ],
        ];
    }
    
    public function actionLogin()
    {
    	if (!\Yii::$app->user->isGuest) {
            if (Yii::$app->getUser()->getIdentity()->isAdmin()) {
                return \Yii::$app->getResponse()->redirect(["loan/index"]);
            } else {
                $this->goHome();
            }
    	}
    	
    	$this->layout = "login";
    	
    	$model = new LoginForm();
    	if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if (Yii::$app->getUser()->getIdentity()->isAdmin()) {
                return \Yii::$app->getResponse()->redirect(["loan/index"]);
            } else {
                return $this->goBack();
            }
    	}
    	return $this->render('login', [
    			'model' => $model,
    	]);
    }
    
    public function actionCalendar()
    {
    	$model = new UserCalendar();
    	$model->user_id = Yii::$app->getUser()->getIdentity()->getId();
    	 
    	if (Yii::$app->request->isPost) {
    		if (Yii::$app->request->post("id")) {
    			$model = UserCalendar::findOne(["id" => Yii::$app->request->post("id")]);
    			if ($model->delete()) {
    				return true;
    			} else {
    				return false;
    			}
    			exit;	
    		}
    		
    		$model->title = Yii::$app->request->post("title");
    		$model->start = Yii::$app->request->post("start");
    		$model->end = Yii::$app->request->post("end");
    		if ($model->save()) {
    			return $model->id;
    		} else {
    			return false;
    		}
    		exit;
    	} else {
    		return $this->render('calendar', [
    				'calendar' => $model,
    		]);
    	}
    }
    
    public function actionLogout()
    {
    	Yii::$app->user->logout();
    
    	return $this->goHome();
    }
    
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->setScenario('insert');

	    $model->auth = ''; // default value

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $this->deleteAvatar($model->id);

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $id
     * @return void
     * @throws NotFoundHttpException
     */
    protected function deleteAvatar($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->post('delete_avatar')) {
            $uploadImage = $model->getBehavior('uploadImageBehavior');
            $uploadImage->afterDelete();

            $model->avatar = null;
            $model->save();
        }
    }

    /**
     * Deletes an existing User model.
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
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
