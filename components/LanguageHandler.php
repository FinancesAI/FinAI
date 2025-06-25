<?php
/**
 * Created by PhpStorm.
 * User: Marcis
 * Date: 05.05.2019.
 * Time: 13:07
 */

namespace app\components;


class LanguageHandler extends \yii\base\Behavior {

	public function events() {
		return [ \yii\web\Application::EVENT_BEFORE_REQUEST => 'handleBeginRequest' ];
	}

	public function handleBeginRequest( $event ) {

		if (\Yii::$app->getUser()->identity) {
			$userLang = \Yii::$app->getUser()->identity->lang;

			if ( $userLang ) {
				\Yii::$app->language = $userLang;
                \Yii::$app->formatter->locale = $userLang;
                \Yii::$app->formatter->language = $userLang;
			}
		}


	}
}