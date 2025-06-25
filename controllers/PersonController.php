<?php

namespace app\controllers;

use Yii;
use app\models\Person;
use app\models\search\PersonSearch;
use app\models\search\LoanSearch;
use yii\filters\AccessControl;
use app\base\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * PersonController implements the CRUD actions for Person model.
 */
class PersonController extends Controller
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
	            'only'  => [ 'index', 'create', 'update', 'edit', 'view' ],
	            'rules' => [
		            [
			            'actions'       => [ 'index', 'update', 'create', 'view' ],
			            'allow'         => true,
			            'matchCallback' => function ( $rule, $action ) {
				            return ! Yii::$app->getUser()->getIdentity()->isBank();
			            },
		            ],
	            ],
            ],
        ];
    }

    /**
     * Lists all Person models.
     * @return mixed
     */
    public function actionIndex()
    {        
        $model = new Person();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        	return $this->redirect(['view', 'id' => $model->id]);
        } else {
        	$searchModel = new PersonSearch();
        	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        	
	        return $this->render('index', [
	            'searchModel' => $searchModel,
	            'dataProvider' => $dataProvider,
	            'model' => $model,
	        ]);
        }
    }

    /**
     * Displays a single Person model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
    	$model = $this->findModel($id);

    	if ($model->load(Yii::$app->request->post()) && $model->save()) {
    		return $this->refresh();
    	} else {
    		$searchModel = new LoanSearch();
    		$searchModel->setAttribute("person_id", $id);
    		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    		
    		return $this->render('view', [
	            'model' => $model,
	        	'searchModel' => $searchModel,
	        	'dataProvider' => $dataProvider,
    		]);
    	}
    }

    /**
     * Creates a new Person model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Person();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Person model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        	
        	if (Yii::$app->request->post("lid")) {
        		return $this->redirect(['/loan/view', 'id' => Yii::$app->request->post("lid")]);
        	}
        	
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Person model.
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
     * Finds the Person model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Person the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Person::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
