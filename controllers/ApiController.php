<?php

namespace app\controllers;

use app\models\Changes;
use app\models\Loan;
use app\models\LoanApi;
use app\models\LoanEnterprise;
use app\models\LoanGuarantor;
use app\models\LoanProgerss;
use app\models\User;
use Yii;
use app\base\Controller;
use yii\helpers\Json;


/**
 * FieldController implements the CRUD actions for Field model.
 */
class ApiController extends Controller {
	/**
	 * Lists all Field models.
	 * @return mixed
	 */

	public function actionPostData() {
		$this->layout = "api";
		$this->_log( "notice - ip try to post data." . ( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : "127.0.0.1" ) );

		if ( Yii::$app->getRequest()->isPost && $this->_validateIP() ) {
			$uniqueId = $_POST["Loan"]["source"] . "-" . $_POST["id"];

			if ( Yii::$app->getRequest()->post( "task" ) == "update" ) {
				$model = Loan::find()->where( [ "unique_id" => $uniqueId ] )->one();

				if ( $model->extra && $model->extra->load( Yii::$app->request->post() ) && $model->extra->save() ) {

					if ( $model->person->load( Yii::$app->request->post() ) ) {
						$model->person->save();
					}

					//if ($model->status == Loan::STATUS_IN_PROGRES) {
					$model->load( Yii::$app->request->post() );
					$model->status = Loan::STATUS_NEW;
					//}

					$model->reminder_time = null;
					$model->update_time   = time();
					$model->save();

					echo Json::encode( [ "status" => true, "save" => true ] );
					$this->_log( "update save - " . json_encode( Yii::$app->request->post() ) );
				} else {
					echo Json::encode( [ "status" => true, "save" => false, "errors" => $model->getErrors() ] );
					$this->_log( "update error - " . json_encode( $model->getErrors() ) . " - " . json_encode( Yii::$app->request->post() ) );
				}
			} else {
				$model            = new Loan();
				$model->unique_id = $uniqueId;

				if ( Loan::find()->where( [ "unique_id" => $uniqueId ] )->one() ) {
					echo Json::encode( [ "status" => true, "save" => true ] );
					$this->_log( "already created - skip - " . json_encode( Yii::$app->request->post() ) );
				} else {
					if ( $model->load( Yii::$app->request->post() ) && $model->save() ) {

						echo Json::encode( [ "status" => true, "save" => true ] );

						if ( $model->description ) {
							Changes::setChanges( Loan::TYPE, $model->id, "description", "", $model->description );
						}

						$this->_log( "save - " . json_encode( Yii::$app->request->post() ) );
					} else {
						echo Json::encode( [ "status" => true, "save" => false, "errors" => $model->getErrors() ] );
						$this->_log( "error - " . json_encode( $model->getErrors() ) . " - " . json_encode( Yii::$app->request->post() ) );
					}
				}
			}

			exit();
		}

		$this->_log( "notice - bad ip try to connect." . ( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : "127.0.0.1" ) );
		exit();
	}

	public function actionReject() {
		$this->layout = "api";
		$this->_log( "notice - ip try to post data." . ( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : "127.0.0.1" ) );

		if ( Yii::$app->getRequest()->isPost && $this->_validateIP() ) {
			$uniqueId = $_POST["Loan"]["source"] . "-" . $_POST["id"];

			$model = Loan::find()->where( [ "unique_id" => $uniqueId ] )->one();

			if ( $model ) {
				$model->status = Loan::STATUS_REJECTED;
				if ( $model->save() ) {
					Changes::setChanges( Loan::TYPE, $model->id, "description", "", "Self rejected from email link." );
					echo Json::encode( [ "status" => true, "save" => true ] );
					$this->_log( "save - " . json_encode( Yii::$app->request->post() ) );
				} else {
					echo Json::encode( [ "status" => true, "save" => false ] );
					$this->_log( "reject save error - " . json_encode( $model->getErrors() ) . " - " . json_encode( Yii::$app->request->post() ) );
				}
			} else {
				echo Json::encode( [ "status" => true, "save" => false ] );
				$this->_log( "reject error - model not found - " . json_encode( Yii::$app->request->post() ) );
			}
		}
	}

	public function actionGetStatus() {
		$this->layout = "api";
		$this->_log( "notice - ip try to get status." . ( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : "127.0.0.1" ) );

		if ( Yii::$app->getRequest()->isPost && $this->_validateIP() ) {
			$uniqueId = $_POST["Loan"]["source"] . "-" . $_POST["id"];

			$model = Loan::find()->where( [ "unique_id" => $uniqueId ] )->one();

			if ( $model ) {
				echo Json::encode( [ "status" => $model->status ] );
			} else {
				echo Json::encode( [ "status" => 0 ] );
			}
		}

		exit();
	}

	public function actionValidateHash() {
		$this->layout = "api";
		$this->_log( "notice - ip try to validate hash." . ( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : "127.0.0.1" ) );

		if ( Yii::$app->getRequest()->isPost && $this->_validateIP() ) {
			//@todo create new action done, that user saw this
			$model = LoanApi::find()->where( [
				"url_hash" => \Yii::$app->getRequest()->post( "hash" ),
				"used"     => 0
			] )->one();

			// Last active allowed only
//     		if ($model) {
//     			$check = LoanApi::find()->where(["loan_id" => $model->loan_id])->orderBy("id desc")->one();
//     			if ($check->id !== $model->id) {
//     				return json_encode(["valid" => false]);
//     			}
//     		}

			if ( $model ) {
				if ( \Yii::$app->getRequest()->post( "task" ) == "update" ) {

					if ( $model->type == 7 ) {

						$gur = LoanGuarantor::find()->where( [ "loan_id" => $model->loan_id ] )->one();
						if ( ! $gur ) {
							$gur          = new LoanGuarantor();
							$gur->loan_id = $model->loan_id;
						}
						$gur->load( \Yii::$app->getRequest()->post() );
						$gur->save();

						$this->_log( "save - user update api - " . json_encode( Yii::$app->request->post() ) . " - error -" . json_encode( $gur->getErrors() ) );

					} elseif ( $model->type == 8 ) {

						$gur = LoanEnterprise::find()->where( [ "loan_id" => $model->loan_id ] )->one();
						if ( ! $gur ) {
							$gur          = new LoanEnterprise();
							$gur->loan_id = $model->loan_id;
						}
						$gur->load( \Yii::$app->getRequest()->post() );
						$gur->save();

						$model->loan->extra->load( \Yii::$app->getRequest()->post() );
						$model->loan->extra->save();

						$this->_log( "save - user update api - " . json_encode( Yii::$app->request->post() ) . " - error -" . json_encode( $gur->getErrors() ) );

					} else {

						$model->loan->extra->load( \Yii::$app->getRequest()->post() );
						$model->loan->extra->save();

						$this->_log( "save - user update api - " . json_encode( Yii::$app->request->post() ) . " - error -" . json_encode( $model->loan->extra->getErrors() ) );
					}

					// update actions
					$loanAction = Loan::find()->where( [ "id" => $model->loan->id ] )->one();
					if ( $loanAction ) {
						if ( in_array( sprintf( "%02d", 81 ), $loanAction->actions ) ) {
							Changes::setChanges( Loan::TYPE, $loanAction->id, "actions_" . sprintf( "%02d", 81 ), null, 1 );
						} else {
							$loanAction->setAttribute( "actions", $loanAction->actions + [ sprintf( "%02d", 81 ) => 1 ] );
							$loanAction->save();
						}
					}

					$model->loan->reminder_time = null;
					$model->loan->status        = Loan::STATUS_NEW;
					$model->loan->save();

					$model->used = 1;
					$model->save();

					return json_encode( [ "valid" => true, "save" => true, "form" => $model->type ] );
				} else {
					// update actions
					if ( in_array( sprintf( "%02d", 80 ), $model->loan->actions ) ) {
						Changes::setChanges( Loan::TYPE, $model->loan->id, "actions_" . sprintf( "%02d", 80 ), null, 1 );
					} else {
						$model->loan->setAttribute( "actions", $model->loan->actions + [ sprintf( "%02d", 80 ) => 1 ] );
						$model->loan->save();
					}
				}

				return json_encode( [ "valid" => true, "save" => false, "form" => $model->type ] );
			} else {
				return json_encode( [ "valid" => false ] );
			}
		}

		\Yii::$app->end();
	}

	public function actionUpdate() {
		$accessToken = \Yii::$app->getRequest()->post( "token" );

		$user = User::findOne( [ 'access_token' => $accessToken, 'role' => 2 ] );

		if ( $user ) {
			$this->updateLoanProgress( $user );
		}
	}

	function updateLoanProgress( $user ) {
		$returnData       = [];
		$response         = Yii::$app->response;
		$response->format = \yii\web\Response::FORMAT_JSON;

		try {
			$provider    = $user->provider_id;
			$id          = \Yii::$app->getRequest()->post( "id" );
			$amount      = \Yii::$app->getRequest()->post( "amount" );
			$text        = \Yii::$app->getRequest()->post( "text" );
			$status      = \Yii::$app->getRequest()->post( "status" );
			$lead_status = \Yii::$app->getRequest()->post( "lead_status" );

			$loanProgress = LoanProgerss::findOne( [ 'api_response_id' => $id, 'provider_id' => $provider ] );

			if ( ! empty( $amount ) ) {
				$loanProgress->amount = $amount;
			}
			if ( ! empty( $text ) ) {
				$loanProgress->text = $text;
			}
			if ( ! empty( $status ) ) {
				$s = (int) $status;
				if ( $s === 1 && $s === 2 && $s === 3 ) {
					$loanProgress->status = $s;
				}
			}
			if ( ! empty( $lead_status ) ) {
				$ls = (int) $lead_status;
				if ( $ls === 1 && $ls === 2 && $ls === 3 ) {
					$loanProgress->lead_status = $ls;
				}
			}

			if ( $loanProgress->save() ) {
				$returnData[] = [ 'success' => true ];
			} else {
				$returnData[] = [ 'success' => false ];
			}

		} catch ( \Exception $e ) {
			$returnData[] = [ 'success' => false ];
		}
		$response->data = $returnData;

		return $response;
	}


	private function _log( $msg ) {
		$fd  = fopen( Yii::$app->params["api_data_log_path"], "a+" );
		$str = "[" . date( "Y/m/d h:i:s", time() ) . "] " . $msg;
		fwrite( $fd, $str . "\n" );
		fclose( $fd );
	}

	/**
	 * validate ip
	 */
	private function _validateIP() {
		return true;
//		$ip = ( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : "127.0.0.1" );
//
//		$validIPs = explode( ",", Yii::$app->setting->get( "api_valid_ip" ) );
//		foreach ( $validIPs as $validIP ) {
//			if ( trim( $validIP ) == $ip ) {
//				return true;
//			}
//		}
//
//		if ( strpos( $ip, '159.148.27' ) !== false ) {
//			return true;
//		}
//
//		return false;
	}
}
