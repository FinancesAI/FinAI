<?php

namespace app\controllers;
use Yii;
use app\models\User;

class LangController extends \yii\web\Controller {
	private $lang;

	public function actionSet() {

		if ( isset( $_GET['lang'] ) ) {
			$this->lang = $_GET['lang'];

			$this->setSiteLanguage();
			return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
		}

	}


	function setSiteLanguage() {
		$user       = User::find()->where( [ 'id' => \Yii::$app->getUser()->id ] )->one();
		$user->lang = $this->lang;
		$user->save();

		$this->redirect( [ 'site/index' ] );
	}

}
