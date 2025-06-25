<?php

namespace app\controllers;

use Yii;
use app\base\Controller;
use yii\base\Object;
use yii\filters\AccessControl;
use app\models\Property;


class DownloadController extends Controller
{
    public function actionIndex($file)
    {
    	if (\Yii::$app->setting->get("export_role") == \Yii::$app->getUser()->getIdentity()->role) {
    		return \Yii::$app->getResponse()->sendFile(__DIR__."/../".$file);
    	}
    	
    	return $this->goBack();
    }
}
