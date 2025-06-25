<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use app\models\Field;
use app\models\FieldView;
use app\base\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use himiklab\sortablegrid\SortableGridAction;


/**
 * FieldController implements the CRUD actions for Field model.
 */
class FieldController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'update', 'delete', 'sort', 'order', 'order-index'],
                'rules' => [
					[
						'actions' => ['index', 'create', 'update', 'delete', 'sort', 'order', 'order-index'],
						'allow' => true,
						'matchCallback' => function($rule, $action) { return Yii::$app->getUser()->getIdentity()->isAdmin(); },
					],
                ],
            ],
        ];
    }
    
    public function actions()
    {
    	return [
    			'sort' => [
    					'class' => SortableGridAction::className(),
    					'modelName' => FieldView::className(),
    			],
    	];
    }
    
    /**
     * Lists all Field models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new Field();
        $dataProvider = new ActiveDataProvider([
            'query' => Field::find()->orderBy("sort_order asc"),
        	'sort'=> false,
        	'pagination' => [
        			'pagesize' => 1000,
        	],
        ]);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Field model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionOrder($id)
    {
    	$model = new FieldView();
    	Yii::$app->getSession()->set("field-order-id", $id);
    	 
    	if (Yii::$app->request->post() && $model->saveFields()) {
    		return $this->refresh();
    	} else {
    		$searchModel = new Field();
    		$dataProvider = new ActiveDataProvider([
    				'query' => FieldView::findFields(Yii::$app->getRequest()->get("id")),
    				'sort'=> false,
    				'pagination' => [
    						'pagesize' => 1000,
    				],
    		]);
    		
    		// check if all fields has add to view start
    		$availableFields = Field::find()->where(["type" => $model->getTypeById($id)])->all();
    		$savedFields = $dataProvider->getModels();
    		$savedFieldsTitles = [];
    		$needRefresh = false;
    		foreach ($savedFields as $field)
    			$savedFieldsTitles[] = $field->name;
    		foreach ($availableFields as $availableField) {
    			if (!in_array($availableField->name, $savedFieldsTitles)) {
    				$new = new FieldView();
    				$new->view = $id;
    				$new->field_id = $availableField->id;
    				$new->save();
    				$needRefresh = true;
    			}
    		}
    		if ($needRefresh)
    			return $this->refresh();
    		// check if all fields has add to view end
    		
    		return $this->render('order', [
    				'model' => $model,
    				'searchModel' => $searchModel,
    				'dataProvider' => $dataProvider,
    		]);
    	}
    }
    
    /**
     * Creates a new Field model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionOrderIndex()
    {
    	$views = (new FieldView())->getViews();
    	
    	$data = [];
    	foreach ($views as $viewId => $viewName)
    		$data[] = ["name" => $viewName, "id" => $viewId];

		$dataProvider = new ArrayDataProvider([
		        'key'=>'id',
		        'allModels' => $data,
		        'sort' => false,
		]);
		
    	return $this->render('order_index', [
    			'dataProvider' => $dataProvider,
    	]);
    }
    
    /**
     * Creates a new Field model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Field();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Field model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Field model.
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
     * Finds the Field model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Field the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Field::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
