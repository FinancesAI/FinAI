<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;

class SmsController extends Controller {
	public function actionSend() {
		echo 'Hello from Console controller';
	}

	public function actionIndex( $category ) {
		$action = '';
		if ( $category == 'waiting_extra' ) {
			$action = 'sendextra';
		} else if ( $category == 'waiting_bs' ) {
			$action = 'sendbs';
		} else if ( $category == 'test' ) {
			$action = 'sendtest';
		}

//		$params = array( 'type' => '2', 'statuses' => [ '11' ] );

		\Yii::$app->controllerNamespace = "app\controllers";

//		$result = \Yii::$app->runAction( 'sms/' . $action );

		$result = $this->run('sms/' . $action );

		echo $result;

	}

	public function actionSendTest() {
		echo 'Action - SendTest';
	}


}
