<?php

namespace app\components\modules;

use app\models\Loan;
use app\services\SourceService;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class LoanTargetCircleBehavior extends Behavior {
	const TYPE_LEAD = 'lead';
	const TYPE_SALES = 'sale';

	public function events() {
		return [
			ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
			ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
		];
	}

	public function afterInsert( $event ) {
		$this->makePost( $event, self::TYPE_LEAD );
		$event->sender->is_tc_sent = 1;
		$event->sender->save();
	}

	function shouldSendLeadRequest( $tmt_data, $event, $eventSid ) {
		if ( ( isset( $tmt_data ) && ! empty( $tmt_data ) && $event->sender->source ) ) {
			$isOneLoan = Loan::find()->where( [ "person_id" => $event->sender->person_id ] )->count() == 1;
			if ( $isOneLoan ) {
				if ( ! empty( $eventSid ) ) {
					return true;
				}
			}
		}

		return false;
	}

	function getSafeEventSid( $event, $type ) {
		$eventId = $this->_getEventSidBySource( $event->sender->source, $type );

		return $eventId ? $eventId : '';
	}

	function _getEventSidBySource( $source, $type ) {
//		$params         = \Yii::$app->params['wordpress']['forms'];
        $params         = SourceService::getWordpressForms();
		$lead_event_sid = null;
		foreach ( $params as $item => $value ) {
			if ( $item == $source ) {
				$sidType        = $type == self::TYPE_LEAD ? 'lead_event_sid' : 'sales_event_sid';
				$lead_event_sid = $value[ $sidType ];
				break;
			}
		}

		return $lead_event_sid;
	}

	function makePost( $event, $type ) {
		$tmt_data = $event->sender->tmt_data;
		$eventSid = $this->getSafeEventSid( $event, $type );

		if ( $this->shouldSendLeadRequest( $tmt_data, $event, $eventSid ) ) {

			$data = [
				'tmt_data'  => $tmt_data,
				'event_sid' => $eventSid,
				"type"      => $type,
				"id"        => $event->sender->id,
			];

			if ( $type == self::TYPE_SALES ) {
				$data += [ 'amount' => $event->sender->amount ];
			}

//			echo print_r( $data );

			$this->_postBack( $data );
		}
	}

	public function afterUpdate( $event ) {
//		echo 'afterUpdate 1, status: ' .$event->sender->status . ',  is_tc_sent: ' .  $event->sender->is_tc_sent . ', id: ' . $event->sender->id;
		if ( $event->sender->status == Loan::STATUS_CLOSED
		     && $event->sender->is_tc_sent ) {

			$this->makePost( $event, self::TYPE_SALES );
		}
	}

	private function _postBack( $data ) {
		$fullUrl = \Yii::$app->params["postbackurl"] . "?" . http_build_query( $data );

		$result = @file_get_contents( $fullUrl );
		if ( $result ) {
			$this->_log( "OK - " . $fullUrl . " - ANSWER - " . json_encode( $result ) );
		} else {
			$this->_log( "ERROR - " . $fullUrl );
		}
	}

	private function _log( $msg ) {
		$fd  = fopen( \Yii::$app->params["postback_log_path"], "a+" );
		$str = "[" . date( "Y/m/d h:i:s", time() ) . "] " . $msg;
		fwrite( $fd, $str . "\n" );
		fclose( $fd );
	}
}