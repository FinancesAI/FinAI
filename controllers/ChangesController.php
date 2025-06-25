<?php

namespace app\controllers;

use Yii;
use app\base\Controller;
use app\models\Loan;
use app\models\Person;
use app\models\Changes;
use yii\helpers\ArrayHelper;

class ChangesController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
            ],
        ];
    }

    public function actionIndex()
    {
    	if (Yii::$app->getRequest()->get("desc")) {
    		if (Yii::$app->getRequest()->post("type") && Yii::$app->getRequest()->post("type_id")) {
    			return Changes::getChanges(explode(",", Yii::$app->getRequest()->post("type")), [Yii::$app->getRequest()->post("type_id")], true);
    		}
    	} else {
    		if (Yii::$app->getRequest()->post("type") && Yii::$app->getRequest()->post("type_id")) {
                return Changes::getChanges(explode(",", Yii::$app->getRequest()->post("type")), [Yii::$app->getRequest()->post("type_id")]);
    		}
    	}

    	
    	Yii::$app->end();
    }

    public function actionAll()
    {
    	if (Yii::$app->getRequest()->post("type_id")) {
    		$model = Loan::find()->where(["id" => Yii::$app->getRequest()->post("type_id")])->one();

    		echo Changes::getChanges(explode(",", Yii::$app->getRequest()->post("type")), ArrayHelper::getColumn($model->person->getLoans()->select("id")->asArray()->all(), "id"));
    	}
    	
    	Yii::$app->end();
    }
}
