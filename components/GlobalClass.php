<?php
namespace app\components;

class GlobalClass {

	/**
	 * Global class that check if user is logged in
	 */
	public function __construct() {

		if (\Yii::$app->getUser()->isGuest
                && \Yii::$app->urlManager->createAbsoluteUrl(["webhook/elizings"]) !== \Yii::$app->getRequest()->getAbsoluteUrl()
				&& \Yii::$app->urlManager->createAbsoluteUrl(["user/login"]) !== \Yii::$app->getRequest()->getAbsoluteUrl()
				&& \Yii::$app->urlManager->createAbsoluteUrl(["api/update"]) !== \Yii::$app->getRequest()->getAbsoluteUrl()
				&& \Yii::$app->urlManager->createAbsoluteUrl(["api/post-data"]) !== \Yii::$app->getRequest()->getAbsoluteUrl()
				&& \Yii::$app->urlManager->createAbsoluteUrl(["api/reject"]) !== \Yii::$app->getRequest()->getAbsoluteUrl()
				&& \Yii::$app->urlManager->createAbsoluteUrl(["api/get-status"]) !== \Yii::$app->getRequest()->getAbsoluteUrl()
				&& \Yii::$app->urlManager->createAbsoluteUrl(["api/validate-hash"]) !== \Yii::$app->getRequest()->getAbsoluteUrl()) {
			\Yii::$app->getResponse()->redirect(["user/login"]);
			\Yii::$app->end();	
		} else {
			\Yii::$app->formatter->dateFormat = "php:".\Yii::$app->setting->get("date_format");
			\Yii::$app->language = \Yii::$app->setting->get("language");
		}
		
		// simple bot check
		if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
			\Yii::$app->getResponse()->setStatusCode("403");
			return \Yii::$app->end();
		}
	}
	
}