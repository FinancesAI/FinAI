<?php

namespace app\controllers;

use Yii;
use app\models\PartialData;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class PartialDataController extends Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
                 'query' => PartialData::find()->orderBy(['created_date' => SORT_DESC]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
     public function actionSendSms()
    {
         $id = Yii::$app->request->post('id');
        //  echo $id;
        // exit;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = PartialData::findOne($id);
        
        if ($model) {
            $model->is_send = 1; // Example modification
            if ($model->save()) {
                return ['success' => true];
            }
        }
        return ['success' => false];
    }
}
