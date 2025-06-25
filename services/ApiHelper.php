<?php

namespace app\services;

class ApiHelper {


	static function isBusinessLoan( $source_id ) {
		if ( $source_id === 9 || $source_id === 10 ) {
			return true;
		}

		return false;
	}

	static function isConsumerLoan( $source_id ) {
		if ( $source_id === 3 || $source_id === 4 ) {
			return true;
		}

		return false;
	}

	static function isRefinancingLoan( $source_id ) {
		if ( $source_id === 11 || $source_id === 12 ) {
			return true;
		}

		return false;
	}

	static function isOnlineLoan( $source_id ) {
		if ( $source_id === 13 || $source_id === 14 ) {
			return true;
		}

		return false;
	}

	static function isAutoLoan( $source_id ) {
		if ( $source_id === 5 || $source_id === 6 ) {
			return true;
		}

		return false;
	}
}
